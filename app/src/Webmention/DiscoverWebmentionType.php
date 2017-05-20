<?php

namespace Aruna\Webmention;

class DiscoverWebmentionType
{
    private $base_url_host = "j4y.co";

    public function __invoke($event)
    {
        if ($event["error"]) {
            $event["type"] = "error";
            return $event;
        }
        $event["type"] = $this->discoverType($event);
        return $event;
    }

    private function discoverType($event)
    {
        $target_url = parse_url($event["target"]);
        if ($target_url["host"] == $this->base_url_host && ($target_url["path"] == "" || $target_url["path"] == "/")) {
            return "homepage";
        }

        $mf2 = $event["mf2"]["items"][0];
        if (isset($mf2["properties"]["in-reply-to"])) {
            return "comment";
        }
        if (isset($mf2["properties"]["like-of"])) {
            return "like";
        }
        return "mention";
    }
}
