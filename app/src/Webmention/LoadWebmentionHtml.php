<?php

namespace Aruna\Webmention;

class LoadWebmentionHtml
{
    public function __construct(
        $log,
        $http,
        $extHtmlRepo
    ) {
        $this->log = $log;
        $this->http = $http;
        $this->extHtmlRepo = $extHtmlRepo;
    }

    public function __invoke($event)
    {
        $event = $this->cleanupUrls($event);
        list(
            $event["mention_source_html"],
            $event["error"]
        ) = $this->fetchMentionHtml($event["source"], $event["uid"]);
        return $event;
    }

    private function cleanupUrls($event)
    {
        $event["source"] = trim($event["source"]);
        $event["target"] = trim($event["target"]);
        $event["mention_id"] = md5(strtolower($event['source'].$event['target']));
        return $event;
    }

    private function fetchMentionHtml($url, $event_id)
    {
        $out = null;
        $error = false;

        $EXT_DIR = "ext/mention_source_html";
        $out_path = $EXT_DIR."/".$event_id.".html";

        if ($this->extHtmlRepo->exists($out_path)) {
            $this->log->debug(sprintf("Fetching HTML from disk"));
            $out = $this->extHtmlRepo->readContents($out_path);
            return [$out, $error];
        }

        try {
            $host = parse_url($url, PHP_URL_HOST);
            $this->log->debug(sprintf("Fetching HTML from host: %s", $host));
            $result = $this->http->request("GET", $url);
            $out = (string) $result->getBody();
            $this->extHtmlRepo->save($out_path, $out);
        } catch (\Exception $e) {
            $this->log->error(sprintf("Failed, received error from url: %s", $e->getMessage()));
            $error = $e->getMessage();
        }

        return [$out, $error];
    }
}
