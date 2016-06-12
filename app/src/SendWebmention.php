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

    # find all urls
    # foreach url:
    #
    # findWebmentionEndpoint()
    #   check for an HTTP Link header [RFC5988] with a rel value of webmention,
    #   or an HTML <link>
    #   or <a> element with a rel value of webmention.
    public function __invoke($event)
    {
        $urls = $this->findUrls($event);
        foreach ($urls as $url) {
            $endpoint = $this->findWebmentionEndpoint($url);
        }
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
        return $this->getEndpointFromLinkHeader($url);
    }

    private function getEndpointFromLinkHeader($url)
    {
        $result = $this->http->request("GET", $url);

        foreach ($result->getHeader('Link') as $link) {
            $hrefandrel = explode('; ', $link);
            $href = trim($hrefandrel[0], '<>');

            $is_webmention = in_array(
                "webmention",
                array_map(
                    "strtolower",
                    explode(
                        " ",
                        str_replace(
                            array("rel=", '"'),
                            null,
                            $hrefandrel[1]
                        )
                    )
                )
            );

            if (isset($hrefandrel[1]) && $is_webmention) {
                return $this->getAbsoluteURL($url, $href);
            }
        }

        return $this->getAbsoluteURL(
            $url,
            $this->parseEndpoint(
                ($this->loadDOM((string) $result->getBody())),
                "webmention"
            )
        );
    }

    private function parseEndpoint($dom, $endpoint_rel)
    {
        foreach ($dom->getElementsByTagName("link") as $link) {

            $is_webmention = in_array(
                "webmention",
                array_map(
                    "strtolower",
                    explode(
                        " ",
                        str_replace(
                            array("rel=", '"'),
                            null,
                            $link->getAttribute("rel")
                        )
                    )
                )
            );

            if ($is_webmention) {
                return $link->getAttribute("href");
            }
        }
        foreach ($dom->getElementsByTagName("a") as $link) {

            $is_webmention = in_array(
                "webmention",
                array_map(
                    "strtolower",
                    explode(
                        " ",
                        str_replace(
                            array("rel=", '"'),
                            null,
                            $link->getAttribute("rel")
                        )
                    )
                )
            );

            if ($is_webmention) {
                return $link->getAttribute("href");
            }
        }
    }

    private function loadDOM($html)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
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
