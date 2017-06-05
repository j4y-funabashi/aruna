<?php

namespace Aruna\Publish;

use \Aruna\PostViewModel;

class SendWebmention
{

    public function __construct(
        $http,
        $discoverEndpoint,
        $findUrls,
        $log,
        $extHtmlRepo
    ) {
        $this->http = $http;
        $this->discoverEndpoint = $discoverEndpoint;
        $this->findUrls = $findUrls;
        $this->log = $log;
        $this->extHtmlRepo = $extHtmlRepo;
    }

    public function __invoke(PostViewModel $post)
    {
        $out_dir = "ext/mention_sent/";
        $urls = $this->findUrls->__invoke($post->toString());
        foreach ($urls as $url) {
            $out_path = $out_dir.md5($post->get("uid").$url).".json";
            if ($this->extHtmlRepo->exists($out_path)) {
                $this->log->debug(sprintf("Fetching HTML from disk"));
                $out = $this->extHtmlRepo->readContents($out_path);
            } else {
                $out = $this->sendWebmention(
                    $post->url(),
                    $url
                );
                $this->extHtmlRepo->save($out_path, json_encode($out));
            }
        }
        return $post;
    }

    private function sendWebmention($source, $target)
    {
        $out = array(
            "source" => $source,
            "target" => $target
        );
        try {
            $get_result = $this->http->request(
                "GET",
                $target,
                [
                    'connect_timeout' => 10,
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
            $m = "Failed to GET ".$target." ".$e->getMessage();
            $this->log->error($m);
            $out["error"] = $m;
            return $out;
        }

        $mention_endpoint = $this->discoverEndpoint->__invoke($url, $get_result, "webmention");
        $out["mention_endpoint"] = $mention_endpoint;

        if ($mention_endpoint === "") {
            return $out;
        }

        $form_params = array("source" => $source, "target" => $target);
        $this->log->info("sending mention to [".$mention_endpoint."]", $form_params);
        try {
            $response = $this->http->request(
                "POST",
                $mention_endpoint,
                [
                    "form_params" => $form_params,
                    'http_errors' => false,
                    'connect_timeout' => 10,
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
            $m = "Failed to POST to ".$mention_endpoint." ".$e->getMessage();
            $this->log->error($m);
            $out["error"] = $m;
            return $out;
        }
        $out["response_status_code"] = $response->getStatusCode();
        $out["response_location"] = $response->getHeader("Location");
        $out["source_html"] = (string) $get_result->getBody();

        return $out;
    }
}
