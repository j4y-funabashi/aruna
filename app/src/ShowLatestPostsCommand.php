<?php

namespace Aruna;

/**
 * Class ShowLatestPostsCommand
 * @author yourname
 */
class ShowLatestPostsCommand
{
    public function __construct(
        $config
    ) {
        $this->page = $config['page'];
        $this->rpp = $config['rpp'];
    }
}
