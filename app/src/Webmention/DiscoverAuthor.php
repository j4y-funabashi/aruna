<?php

namespace Aruna\Webmention;

class DiscoverAuthor
{
    public function __invoke($event)
    {
        if ($event["error"]) {
            return $event;
        }
        $event = $this->getMicroformats($event);
        $event["author"] = $this->findAuthor($event["mf2"]);
        return $event;
    }

    private function getMicroformats($event)
    {
        $source_base = $this->getBaseUrl($event['source']);
        $event["mf2"] = \Mf2\parse($event['mention_source_html'], $source_base);
        return $event;
    }
    private function getBaseUrl($url)
    {
        $source_parts = parse_url($url);
        return $source_parts['scheme']."://".$source_parts['host'];
    }
    private function isUrl($url)
    {
        $source_parts = parse_url($url);
        return (isset($source_parts['scheme']) && isset($source_parts['host']));
    }
    private function findAuthor($mf2)
    {
        if (empty($mf2["items"])) {
            return null;
        }

        $entry = $this->findFirst($mf2, "h-entry");
        if (isset($entry["properties"]["author"][0]["type"]) && $entry["properties"]["author"][0]["type"][0] == "h-card") {
            return $entry["properties"]["author"][0];
        }
        $feed = $this->findFirst($mf2, "h-feed");
        if (isset($feed["properties"]["author"][0]["type"]) && $feed["properties"]["author"][0]["type"][0] == "h-card") {
            return $feed["properties"]["author"][0];
        }

        if (isset($entry["properties"]["author"][0])) {
            if (false === $this->isUrl($entry["properties"]["author"][0])) {
                return $this->createHcardWithAuthor($entry["properties"]["author"][0]);
            }
            if ($this->isUrl($entry["properties"]["author"][0])) {
                return $this->findRepresentativeHCard($entry["properties"]["author"][0]);
            }
        }
        if (isset($feed["properties"]["author"][0])) {
            if (false === $this->isUrl($feed["properties"]["author"][0])) {
                return $this->createHcardWithAuthor($feed["properties"]["author"][0]);
            }
            if ($this->isUrl($feed["properties"]["author"][0])) {
                return $this->findRepresentativeHCard($feed["properties"]["author"][0]);
            }
        }

        if ($this->isUrl($mf2["rels"]["author"][0])) {
            return $this->findRepresentativeHCard($mf2["rels"]["author"][0]);
        }
    }
    private function createHcardWithAuthor($author_name)
    {
        return [
            "type" => ["h-card"],
            "properties" => [
                "name" => [$author_name],
                "url" => [],
                "photo" => [],
            ]
        ];
    }
    private function findFirst($mf2, $type)
    {
        foreach ($mf2["items"] as $item) {
            if ($item["type"][0] == $type) {
                return $item;
            }
        }
    }
    private function findRepresentativeHCard($url)
    {
        $mf2 = \Mf2\fetch($url);
        return \BarnabyWalters\Mf2\getRepresentativeHCard($mf2, $url);
    }
}
