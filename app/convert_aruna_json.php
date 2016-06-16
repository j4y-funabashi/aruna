<?php

require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\ErrorHandler::register();

function list_files($in_dir) {
	$directory = new RecursiveDirectoryIterator($in_dir);
	$iterator = new RecursiveIteratorIterator($directory);
	return new RegexIterator($iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);
}

function main() {
	require_once __DIR__ . "/app.php";
	$in_dir = "/home/jayr/Desktop";
	$out_dir = "/home/jayr/Desktop/html_posts";
	$files_parsed = 0;

	foreach (list_files($in_dir) as $file) {

		// read post_data
		$in_filename = $file[0];
		$out_filename = $out_dir."/".basename($in_filename, ".json").".html";
		$post_data = json_decode(file_get_contents($in_filename), true);

		// convert post_data to mf json
		$mf_array = Aruna\PostData::toMfArray($post_data);

		// convert mf json to viewModel
		$view_model = new Aruna\PostViewModel($mf_array);

		// render viewModel as html
		$post_html = $app['twig']->render(
			"post_".$view_model->type().".html",
			array("post" => $view_model)
		);

        file_put_contents($out_filename, $post_html);
        $files_parsed += 1;
    }
}

main();
