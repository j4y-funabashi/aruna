<?php

require_once __DIR__.'/vendor/autoload.php';

/*
 * ERROR HANDLING
 */
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('display_startup_errors', 'On');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

$app = new Silex\Application();
$app['debug'] = true;

$app->get("/micropub", function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return "Micropub form goes here";
});

$app->post('/micropub', function (Symfony\Component\HttpFoundation\Request $request) use ($app) {

    $TMP_FILES_DIR = "/tmp/aruna";
    $adapter = new League\Flysystem\Adapter\Local("/tmp/aruna");
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $noteStore = new Aruna\EntryRepository($filesystem);
    $handler = new Aruna\CreateEntryHandler($noteStore);

    // build $entry array from request parameters
    $entry = [];
    foreach ($request->request->all() as $key => $value) {
        $entry[$key] = $value;
    }
    // build files array
    $files = [];
    foreach ($request->files as $file_key => $uploadedFile) {
        $files[$file_key] = $uploadedFile;
    }

    $command = new Aruna\CreateEntryCommand($entry, $files);
    $newEntry = $handler->handle($command);

    return $newEntry->asJson();
});

$app->run();
