<?php

require_once __DIR__ . "/common.php";

$posts_root = "/tmp/aruna/posts";
$resized_root = "/tmp/aruna/static_img";

// GET SORTED LIST OF ALL JSON FILES
$dir = new CallbackFilterIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($posts_root)
    ),
    function ($current, $key, $iterator) {
        return substr($current, -4) == "json";
    }
);
$json_files = [];
foreach ($dir as $fileInfo) {
    $json_files[$fileInfo->getRealPath()] = $fileInfo;
}
ksort($json_files);





Intervention\Image\ImageManagerStatic::configure(array('driver' => 'imagick'));
foreach ($json_files as $fileInfo) {
    // read post array from json file
    $fileObject = $fileInfo->openFile();
    $data = [];
    while (!$fileObject->eof()) {
        $data[] = $fileObject->fgets();
    }
    $post = json_decode(implode("\n", $data), true);

    // resize photo
    if (isset($post['files']['photo'])) {

        $photo_path = $posts_root."/".$post['files']['photo'];
        $photo = new SplFileInfo($photo_path);
        $out_path = $resized_root."/".$photo->getBaseName();

        $img = Intervention\Image\ImageManagerStatic::make($photo->getRealPath());
        $img->fit(1080);
        $img->save($out_path);
        echo sprintf("\nResized %s to %s\n", $photo_path, $out_path);

    }
}
