<?php

namespace Aruna;

class SendWebmention
{

    public function __construct(
        $http,
        $discoverEndpoint,
        $findUrls,
        $log
    ) {
        $this->http = $http;
        $this->discoverEndpoint = $discoverEndpoint;
        $this->findUrls = $findUrls;
        $this->log = $log;
    }

    public function __invoke($event)
    {
        foreach ($this->findUrls->__invoke(json_encode($event, JSON_UNESCAPED_SLASHES)) as $url) {
            $this->log->info("Finding webmention endpoint [".$url."]");
            $result = $this->http->request("GET", $url);
            $mention_endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");
            $source_url = "http://j4y.co/p/".$event['uid'];

            $form_params = array(
                "source" => $source_url,
                "target" => $url
            );
            $response = $this->http->request(
                "POST",
                $mention_endpoint,
                ["form_params" => $form_params]
            );

        }
        return $event;
    }
}
