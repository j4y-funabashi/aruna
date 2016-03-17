<?php

namespace Aruna;

/**
 * Class EventStore
 * @author yourname
 */
class EventStore
{
    public function __construct(
        $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function save(
        $file_path,
        $data
    ) {

        try {
            $this->filesystem->write(
                $file_path,
                $data
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function exists($file_path)
    {
        return $this->filesystem->has($file_path);
    }

    public function readContents($file_path)
    {
        return json_decode($this->filesystem->read($file_path), true);
    }

    public function readById($post_id)
    {
        $post_filepath = array_shift(
            array_filter(
                $this->getJsonFilePaths(),
                function ($file_path) use ($post_id) {
                    return $file_path['filename'] == $post_id;
                }
            )
        );
        return json_decode($this->filesystem->read($post_filepath['path']), true);
    }

    public function listFromId(
        $root_dir,
        $from_id,
        $rpp
    ) {
        $all_paths = $this->getJsonFilePaths($root_dir);
        $key = array_search($from_id, array_column($all_paths, 'filename'));
        return array_filter(
            array_map(
                function ($post_filepath) {
                    return json_decode($this->filesystem->read($post_filepath['path']), true);
                },
                array_slice($all_paths, $key, $rpp)
            )
        );
    }

    private function getJsonFilePaths($root_dir = '')
    {
        $files = array_values(
            array_filter(
                $this->filesystem->listContents($root_dir, true),
                function ($file_path) {
                    return substr($file_path['path'], -4) == "json";
                }
            )
        );
        $out = [];
        foreach ($files as $file) {
            $out[$file['path']] = $file;
        }
        ksort($out);
        return $out;
    }
}
