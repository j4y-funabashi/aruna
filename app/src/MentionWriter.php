<?php

namespace Aruna;

/**
 * Class MentionWriter
 * @author yourname
 */
class MentionWriter
{
    public function __construct(
        $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function save(
        $mention
    ) {

        try {
            $this->filesystem->write(
                $mention['uid'].".json",
                json_encode($mention)
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
