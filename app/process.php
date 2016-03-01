<?php

require_once __DIR__ . "/../common.php";

function listJsonFiles($root_dir)
{
    $dir = new CallbackFilterIterator(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root_dir)
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
    return $json_files;
}

function readFileContents($fileInfo)
{
    // read post array from json file
    $fileObject = $fileInfo->openFile();
    $data = [];
    while (!$fileObject->eof()) {
        $data[] = $fileObject->fgets();
    }
    return json_decode(implode("\n", $data), true);
}



use League\Pipeline\Pipeline;
use Aruna\Action\ImageResizer;
use Aruna\Action\ResizePhoto;

$posts_root = "/tmp/aruna/posts";
$thumbnails_root = "/tmp/aruna/thumbnails";

// BUILD PIPELINE
$pipeline = (new Pipeline())
    ->pipe(
        new ResizePhoto(
            new ImageResizer($posts_root, $thumbnails_root)
        )
    );

// PUSH CONTENTS OF EACH FILE THROUGH PIPELINE
foreach (listJsonFiles($posts_root) as $fileInfo) {
    try {
        $pipeline->process(readFileContents($fileInfo));
    } catch (Exception $e) {
        $m = sprintf(
            "Could not process %s [%s]",
            $fileInfo->getRealPath(),
            $e->getMessage()
        );
        var_dump($m);
    }
}
