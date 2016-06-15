<?php

function list_files($in_dir) {
    $directory = new RecursiveDirectoryIterator($in_dir);
    $iterator = new RecursiveIteratorIterator($directory);
    return new RegexIterator($iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);
}

function post_data_to_mf($post_data) {

    // h
    $h = (isset($post_data['h']))
        ? $post_data['h']
        : "entry";
    unset($post_data['h']);

    // category
    if (isset($post_data['category']) && is_array($post_data['category'])) {
        $post_data['category'] = $post_data['category'];
    }
    if (isset($post_data['category']) && !is_array($post_data['category'])) {
        $post_data['category'] = array_filter(
            array_map(
                "trim",
                explode(",", $post_data['category'])
            )
        );
    }

    // files
    if (isset($post_data['files'])) {
        foreach ($post_data['files'] as $file_key => $file_path) {
            $post_data[$file_key] = array($file_path);
        }
        unset($post_data['files']);
    }

    // content
    // TODO markdownify
    if (isset($post_data['content'])) {
        $content = array(
            array(
                "value" => $post_data['content'],
                "html" => ""
            )
        );
        $post_data['content'] = $content;
    }
    // all other properties
    $properties = array();
    foreach ($post_data as $k => $v) {
        $properties[$k] = $v;
    }

    // build mfArray
    $converted = array(
        "type" => array("h-".$h),
        "properties" => $properties
    );
    $out = array(
        "items" => array()
    );
    $out['items'][] = $converted;

    return $out;
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
        $mf_array = post_data_to_mf($post_data);
        var_dump($mf_array);

        // convert mf json to viewModel
        // render viewModel as html

        $files_parsed += 1;
    }
}

main();
