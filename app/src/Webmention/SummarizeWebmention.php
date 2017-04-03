<?php

namespace Aruna\Webmention;

class SummarizeWebmention
{
    public function __construct($log)
    {
        $this->log = $log;
    }

    public function __invoke($event)
    {
        if ($event["error"] != null) {
            $this->log->notice($event["error"]);
            return $event;
        }
        $mention_view_model = $this->getViewModel($event);
        if ($mention_view_model == null) {
            return $event;
        }
        $out = sprintf(
            "[%s] %s %s your post [%s][%s]",
            $mention_view_model->published(),
            $mention_view_model->authorName(),
            $mention_view_model->type(),
            $event["target"],
            $event["source"]
        );
        $this->log->notice($out);
        return $event;
    }

    private function getViewModel($event)
    {
        $source_base = $this->getBaseUrl($event['source']);
        $mf2 = \Mf2\parse($event['mention_source_html'], $source_base)["items"][0];
        if (!$mf2) {
            return null;
        }
        return new \Aruna\PostViewModel($mf2);
    }
    private function getBaseUrl($url)
    {
        $source_parts = parse_url($url);
        return $source_parts['scheme']."://".$source_parts['host'];
    }
}
