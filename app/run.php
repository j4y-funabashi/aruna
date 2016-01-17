<?php

// php settings
ini_set('memory_limit', '1024M');
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";

Symfony\Component\Debug\ErrorHandler::register();

$app = new \Cilex\Application('Aruna');

$app->command(new \CLI\CreateEntryCommand());
$app->run();
