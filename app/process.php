<?php

require_once __DIR__ . "/../common.php";
$posts_root = "/tmp/aruna/posts";
$thumbnails_root = "/tmp/aruna/thumbnails";

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



// BUILD PIPELINE
use League\Pipeline\Pipeline;
use Aruna\Action\ImageResizer;
use Aruna\Action\ResizePhoto;

$pipeline = (new Pipeline())
    ->pipe(new ResizePhoto(new ImageResizer($posts_root, $thumbnails_root)));

// PUSH CONTENTS OF EACH FILE THROUGH PIPELINE
foreach ($json_files as $fileInfo) {
    // read post array from json file
    $fileObject = $fileInfo->openFile();
    $data = [];
    while (!$fileObject->eof()) {
        $data[] = $fileObject->fgets();
    }
    $post = json_decode(implode("\n", $data), true);

    try {
        $pipeline->process($post);
    } catch (Exception $e) {
        print PHP_EOL."CRITICAL:";
        var_dump($e->getMessage());
    }
}
