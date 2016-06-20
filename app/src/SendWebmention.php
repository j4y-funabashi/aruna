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

    public function __invoke($post)
    {
        foreach ($this->findUrls->__invoke($post->toString()) as $url) {

            $this->log->info("Finding webmention endpoint [".$url."]");

            try {
                $result = $this->http->request("GET", $url);
            } catch (\Exception $e) {
                $m = "Failed to GET ".$url." ".$e->getMessage();
                $this->log->error($m);
                continue;
            }


            $mention_endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");

            $source_url = $post->get('url');
            $form_params = array("source" => $source_url, "target" => $url);

            if ($mention_endpoint != "") {
                $this->log->info("sending mention to [".$mention_endpoint."]", $form_params);

                try {
                    $response = $this->http->request(
                        "POST",
                        $mention_endpoint,
                        ["form_params" => $form_params]
                    );
                } catch (\Exception $e) {
                    $m = "Failed to POST to ".$mention_endpoint." ".$e->getMessage();
                    $this->log->error($m);
                    continue;
                }
            }
        }
        return $post;
    }
}
