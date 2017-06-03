<?php

namespace Aruna\Publish;

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

            try {
                $result = $this->http->request("GET", $url);
            } catch (\Exception $e) {
                $m = "Failed to GET ".$url." ".$e->getMessage();
                $this->log->error($m);
                continue;
            }

            $mention_endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");

            if ($mention_endpoint === "") {
                continue;
            }

            $source_url = $post->url();
            $form_params = array("source" => $source_url, "target" => $url);

            $this->log->info("sending mention to [".$mention_endpoint."]", $form_params);

            try {
                $response = $this->http->request(
                    "POST",
                    $mention_endpoint,
                    ["form_params" => $form_params, 'http_errors' => false]
                );
            } catch (\Exception $e) {
                $m = "Failed to send mention to ".$mention_endpoint." ".$e->getMessage();
                $this->log->error($m);
                continue;
            }

            $form_params["response_status_code"] = $response->getStatusCode();
            $form_params["response_location"] = $response->getHeader("Location");

            $this->log->info("Got response: ", $form_params);
        }
        return $post;
    }
}
