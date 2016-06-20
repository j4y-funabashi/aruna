<?php

namespace Aruna;

class DiscoverEndpoints
{
    public function __invoke($url, $result, $rel_value)
    {
        return (false === $endpoint = $this->getEndpointWithRelValue($result, $rel_value))
            ? ""
            : $this->getAbsoluteURL($url, $endpoint);
    }

    private function getEndpointWithRelValue($result, $rel_value)
    {
        foreach ($result->getHeader('Link') as $links) {
            foreach (explode(", ", $links) as $link) {
                $hrefandrel = explode('; ', $link);
                $href = trim($hrefandrel[0], '<>');
                if (isset($hrefandrel[1]) && $this->relExists($hrefandrel[1], $rel_value)) {
                    return $href;
                }
            }
        }

        return $this->parseEndpoint(
            ($this->loadDOM((string) $result->getBody())),
            $rel_value
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

    private function parseEndpoint($dom, $rel_value)
    {
        $xpath = new \DOMXpath($dom);
        foreach ($xpath->query('//a | //link') as $link) {
            if ($this->relExists($link->getAttribute("rel"), $rel_value)) {
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
        if ($relative_url == "") {
            return $source_url;
        }
        $parsed_url = array_merge(parse_url($source_url), parse_url($relative_url));
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$host$path$query$fragment";
    }
}
