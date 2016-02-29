<?php

namespace Aruna\Action;

/**
 * Class ResizePhoto
 * @author yourname
 */
class ResizePhoto
{
    public function __construct(
        $resizer
    ) {
        $this->resizer = $resizer;
    }

    public function __invoke($post)
    {
        if (isset($post['files']['photo'])) {
            $this->resizer->resize($post['files']['photo']);
        }

        return $post;
    }
}
