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
        $this->filesystem->write(
            $file_path,
            $data
        );
    }

    public function exists($file_path)
    {
        return $this->filesystem->has($file_path);
    }

    public function readContents($file_path)
    {
        return $this->filesystem->read($file_path);
    }

    public function readById($post_id)
    {
        $post_filepath = array_shift(
            array_filter(
                $this->findByExtension(),
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
        $all_paths = $this->findByExtension($root_dir);

        $key = array_search($from_id, array_column($all_paths, 'filename'));
        if (false === $key) {
            $key = 0;
        } else {
            $key += 1;
        }

        return array_filter(
            array_map(
                function ($post_filepath) {
                    $file_contents = $this->filesystem->read($post_filepath['path']);
                    return json_decode($file_contents, true);
                },
                array_slice($all_paths, $key, $rpp)
            )
        );
    }

    public function findByExtension(
        $root_dir = null,
        $extension = "json",
        $limit = 0
    ) {

        $files = array_values(
            array_filter(
                $this->filesystem->listContents($root_dir, true),
                function ($file_path) use ($extension) {
                    return $file_path['extension'] == $extension;
                }
            )
        );
        $out = [];
        foreach ($files as $file) {
            $out[$file['path']] = $file;
        }
        ksort($out);
        if ($limit > 0) {
            $out = array_slice(
                $out,
                0,
                $limit
            );
        }

        return $out;
    }

    public function delete($file_path)
    {
        $this->filesystem->delete($file_path);
    }
}
