<?php

namespace Aruna;

use IndieWeb;

class SendWebmention
{

    public function __construct(
        $http,
        $discoverEndpoint
    ) {
        $this->http = $http;
        $this->discoverEndpoint = $discoverEndpoint;
    }

    public function __invoke($event)
    {
        $urls = $this->findUrls($event);
        $url = $urls[0];
        $result = $this->http->request("GET", $url);
        $endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");
        return $endpoint;
    }

    private function findUrls($event)
    {
        $regex="#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))#";
        preg_match_all($regex, implode("", $event), $matches);
        $out = array();
        foreach ($matches[0] as $match) {
            $out[] = $match;
        }
        return array_unique(array_filter($out));
    }
}
