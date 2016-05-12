<?php

namespace Aruna;

/**
 * Class ShowLatestPostsAction
 * @author yourname
 */
class ShowLatestPostsAction extends Action
{

    protected function getCommand()
    {
        return new ShowLatestPostsCommand(
            array(
                "page" => 1,
                "rpp" => 1
            )
        );
    }
}
