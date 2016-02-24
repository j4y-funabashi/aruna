<?php

require_once __DIR__ . "/common.php";

$filestore_root = "/tmp/aruna";

// GET SORTED LIST OF ALL JSON FILES
$dir = new CallbackFilterIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($filestore_root)
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





foreach ($json_files as $fileInfo) {
    // read data from file
    $fileObject = $fileInfo->openFile();
    $data = [];
    while (!$fileObject->eof()) {
        $data[] = $fileObject->fgets();
    }
    $post = json_decode(implode("\n", $data), true);
    var_dump($post);
}
