<?php

require_once __DIR__ . "/../common.php";

function listJpgFiles($root_dir)
{
    $dir = new CallbackFilterIterator(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root_dir)
        ),
        function ($current, $key, $iterator) {
            return substr($current, -3) == "jpg";
        }
    );
    $file_list = [];
    foreach ($dir as $fileInfo) {
        $file_list[$fileInfo->getRealPath()] = $fileInfo;
    }
    ksort($file_list);
    return $file_list;
}

use Intervention\Image\ImageManagerStatic as Image;

Image::configure(array('driver' => 'imagick'));

$http = new GuzzleHttp\Client();
$root_dir = "/home/jayr/Pictures/2015";
$micropub_endpoint = "http://lagertha.local/micropub";

foreach (listJpgFiles($root_dir) as $fileInfo) {

    $in_path = $fileInfo->getRealPath();

    $img = Image::make($in_path);
    $iptc = $img->iptc();
    if (false === $published = DateTimeImmutable::createFromFormat("Y:m:d H:i:s", $img->exif('DateTimeOriginal'))) {
        $m = sprintf(
            "Skipping %s [couldnt parse date]",
            $in_path
        );
        print $m.PHP_EOL;
        continue;
    }

    $post = [
        [
            'name' => 'h',
            'contents' => 'entry'
        ],
        [
            'name' => 'published',
            'contents' => $published->format('c')
        ],
        [
            'name' => 'photo',
            'contents' => fopen($in_path, 'r')
        ]
    ];
    if (isset($iptc['Headline'])) {
        $post[] = ['name' => 'title', 'contents' => $iptc['Headline']];
    }
    if (isset($iptc['Keywords'])) {
        $post[] = ['name' => 'category', 'contents' => implode(" ", $iptc['Keywords'])];
    }

    $http->request('POST', $micropub_endpoint, ['multipart' => $post]);
}
