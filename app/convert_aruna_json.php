<?php

require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\ErrorHandler::register();

function list_files($in_dir) {
    $directory = new RecursiveDirectoryIterator($in_dir);
    $iterator = new RecursiveIteratorIterator($directory);
    return new RegexIterator($iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);
}

function main() {
    $in_dir = "/home/jayr/Desktop";
    $files_parsed = 0;
    foreach (list_files($in_dir) as $file) {
        // read post_data
        $out = array();
        $in_filename = $file[0];
        $out_filename = basename($in_filename, ".json").".html";
        $post_data = json_decode(file_get_contents($in_filename), true);
        unset($post_data['access_token']);

        // convert post_data to mf json
        $mf_array = Aruna\PostData::toMfArray($post_data);
        var_dump(json_encode($mf_array, JSON_PRETTY_PRINT));

        // convert mf json to viewModel
        // render viewModel as html

        $files_parsed += 1;
    }
}

main();
