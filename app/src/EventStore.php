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
}
