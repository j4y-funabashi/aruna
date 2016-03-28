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
        return new ShowLatestPostsCommand();
    }
}
