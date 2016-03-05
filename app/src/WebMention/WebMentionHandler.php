<?php

namespace Aruna\WebMention;

/**
 * Class WebMentionHandler
 * @author yourname
 */
class WebMentionHandler
{

    public function recieve(array $mention)
    {
        if (
            false === isset($mention['target'])
            || false === isset($mention['source'])
        ) {
            throw new \InvalidArgumentException();
        }
    }
}
