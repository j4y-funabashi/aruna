<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app->command(new CLI\ProcessCacheCommand());

$app->run();
