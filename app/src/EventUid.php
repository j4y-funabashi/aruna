<?php

namespace Aruna;

/**
 * Class EventUid
 * @author yourname
 */
class EventUid
{
    public function __construct($uid = null)
    {
        $this->uid = (null === $uid)
            ? uniqid()
            : $uid;
    }
}
