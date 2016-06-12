<?php

namespace Aruna;

class SendWebmention
{

    public function __construct(
        $http,
        $discoverEndpoint,
        $findUrls
    ) {
        $this->http = $http;
        $this->discoverEndpoint = $discoverEndpoint;
        $this->findUrls = $findUrls;
    }

    public function __invoke($event)
    {
        foreach ($this->findUrls->__invoke(implode(" ", $event)) as $url) {
            $result = $this->http->request("GET", $url);
            $endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");
        }
        return $endpoint;
    }
}
