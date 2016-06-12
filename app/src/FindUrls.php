<?php

namespace Aruna;

class FindUrls
{
    public function __invoke($in)
    {
        $regex="#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))#";
        preg_match_all($regex, $in, $matches);
        $out = array();
        foreach ($matches[0] as $match) {
            $out[] = $match;
        }
        return array_unique(array_filter($out));
    }
}
