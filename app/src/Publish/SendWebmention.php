<?php

namespace Aruna\Publish;

use Aruna\PostViewModel;

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
                $result = $this->http->request(
                    "GET",
                    $url,
                    [
                        'connect_timeout' => 5,
                        'allow_redirects' => [
                            'max'             => 10,
                            'strict'          => false,
                            'referer'         => false,
                            'protocols'       => ['http', 'https'],
                            'track_redirects' => false
                        ]
                    ]
                );
            } catch (\Exception $e) {
                $m = "Failed to GET ".$url." ".$e->getMessage();
                $this->log->error($m);
                continue;
            }

            $mention_endpoint = $this->discoverEndpoint->__invoke($url, $result, "webmention");
            if ($mention_endpoint === "") {
                continue;
            }

            try {
                $this->sendWebmention(
                    $post->url(),
                    $url,
                    $mention_endpoint
                );
            } catch (\Exception $e) {
                $m = "Failed to send mention to ".$mention_endpoint." ".$e->getMessage();
                $this->log->error($m);
                continue;
            }
        }
        return $post;
    }

    private function sendWebmention($source, $target, $mention_endpoint)
    {
        $form_params = array("source" => $source, "target" => $target);
        $this->log->info("sending mention to [".$mention_endpoint."]", $form_params);
        $response = $this->http->request(
            "POST",
            $mention_endpoint,
            [
                "form_params" => $form_params,
                'http_errors' => false,
                'connect_timeout' => 5,
                'allow_redirects' => [
                    'max'             => 10,
                    'strict'          => false,
                    'referer'         => false,
                    'protocols'       => ['http', 'https'],
                    'track_redirects' => false
                ]
            ]
        );
        $form_params["response_status_code"] = $response->getStatusCode();
        $form_params["response_location"] = $response->getHeader("Location");
        $this->log->info("Got response: ", $form_params);
    }
}
