<?php

namespace Aruna;

use IndieWeb;

class SendWebmention
{

    public function __construct(
        $http
    ) {
        $this->http = $http;
    }

    public function __invoke($event)
    {
        $urls = $this->findUrls($event);
        $url = $urls[0];
        $endpoint = $this->findWebmentionEndpoint($url);
        return $endpoint;
    }

    private function findUrls($event)
    {
        $regex="#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))#";
        preg_match_all($regex, implode("", $event), $matches);
        $out = array();
        foreach ($matches[0] as $match) {
            $out[] = $match;
        }
        return array_unique(array_filter($out));
    }

    private function findWebmentionEndpoint($url)
    {
        return (false === $endpoint = $this->getEndpointFromLinkHeader($url))
            ? ""
            : $this->getAbsoluteURL($url, $endpoint);
    }

    private function getEndpointFromLinkHeader($url)
    {
        $result = $this->http->request("GET", $url);

        foreach ($result->getHeader('Link') as $links) {
            foreach (explode(", ", $links) as $link) {
                $hrefandrel = explode('; ', $link);
                $href = trim($hrefandrel[0], '<>');
                if (isset($hrefandrel[1]) && $this->relExists($hrefandrel[1], "webmention")) {
                    return $href;
                }
            }
        }

        return $this->parseEndpoint(
            ($this->loadDOM((string) $result->getBody())),
            "webmention"
        );
    }

    private function relExists($rel_values, $rel_key)
    {
        return in_array(
            $rel_key,
            array_map(
                "strtolower",
                explode(
                    " ",
                    str_replace(
                        array("rel=", '"'),
                        null,
                        $rel_values
                    )
                )
            )
        );
    }

    private function parseEndpoint($dom, $endpoint_rel)
    {
        $xpath = new \DOMXpath($dom);
        foreach ($xpath->query('//a | //link') as $link) {
            if ($this->relExists($link->getAttribute("rel"), "webmention")) {
                return $link->getAttribute("href");
            }
        }
        return false;
    }

    private function loadDOM($html)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        try {
            $dom->loadHTML($html);
        } catch (\Exception $e) {
        }
        return $dom;
    }

    private function getAbsoluteURL($source_url, $relative_url)
    {
        $parsed_url = array_merge(parse_url($source_url), parse_url($relative_url));
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$host$path$query$fragment";
    }
}
