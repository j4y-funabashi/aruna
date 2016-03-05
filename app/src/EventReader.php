<?php

namespace Aruna;

/**
 * Class EventReader
 * @author yourname
 */
class EventReader
{
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function findById($post_id)
    {
        $post_filepath = array_shift(
            array_filter(
                $this->getJsonFilePaths(),
                function ($file_path) use ($post_id) {
                    return $file_path['filename'] == $post_id;
                }
            )
        );
        return [json_decode($this->filesystem->read($post_filepath['path']), true)];
    }

    public function listFromId($from_id, $rpp)
    {
        $all_paths = $this->getJsonFilePaths();
        $key = array_search($from_id, array_column($all_paths, 'filename'));
        return array_values(
            array_map(
                function ($post_filepath) {
                    return json_decode($this->filesystem->read($post_filepath['path']), true);
                },
                array_slice($all_paths, $key, $rpp)
            )
        );
    }

    private function getJsonFilePaths()
    {
        $files = array_values(
            array_filter(
                $this->filesystem->listContents('', true),
                function ($file_path) {
                    return substr($file_path['path'], -4) == "json";
                }
            )
        );
        $out = [];
        foreach ($files as $file) {
            $out[$file['path']] = $file;
        }
        krsort($out);
        return $out;
    }
}
