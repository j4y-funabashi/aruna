<?php


require_once __DIR__.'/vendor/autoload.php';

Symfony\Component\Debug\ErrorHandler::register();

$dotenv = new Dotenv\Dotenv("..");
$dotenv->load();
$filestore_root = "/tmp/aruna";




$dir = new CallbackFilterIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($filestore_root)
    ),
    function ($current, $key, $iterator) {
        return substr($current, -4) == "json";
    }
);

foreach ($dir as $file) {
    var_dump($file);
}
