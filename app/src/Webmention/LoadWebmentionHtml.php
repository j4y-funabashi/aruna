<?php

namespace Aruna\Webmention;

class LoadWebmentionHtml
{
    public function __construct(
        $log,
        $http
    ) {
        $this->log = $log;
        $this->http = $http;
    }

    public function __invoke($event)
    {
        $event["mention_id"] = md5(strtolower($event['source'].$event['target']));
        list(
            $event["mention_source_html"],
            $event["error"]
        ) = $this->fetchMentionHtml($event["source"]);
        return $event;
    }

    private function fetchMentionHtml($url)
    {
        $out = null;
        $error = null;
        $this->log->debug(sprintf("Fetching HTML from url: %s", $url));
        try {
            $result = $this->http->request("GET", $url);
            $out = (string) $result->getBody();
        } catch (\Exception $e) {
            $this->log->error(sprintf("Failed, received error from url: %s", $e->getMessage()));
            $error = $e->getMessage();
        }
        return [$out, $error];
    }
}
