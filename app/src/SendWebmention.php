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
        foreach ($this->findUrls->__invoke(print_r($event, true)) as $url) {
            $this->log->info("Finding webmention endpoint [".$url."]");
            $result = $this->http->request("GET", $url);
            $mention_endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");

            $source_url = "http://j4y.co/p/".$event['uid'];
            $form_params = array("source" => $source_url, "target" => $url);

            if ($mention_endpoint != "") {
                $this->log->info("no endpoint found [".$url."]");
                $response = $this->http->request(
                    "POST",
                    $mention_endpoint,
                    ["form_params" => $form_params]
                );
            }
        }
        return $event;
    }
}
