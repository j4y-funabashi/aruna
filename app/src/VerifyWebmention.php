<?php

namespace Aruna;

/**
 * Class VerifyWebmention
 * @author yourname
 */
class VerifyWebmention
{

    public function __construct(
        $log,
        $http
    ) {
        $this->log = $log;
        $this->http = $http;
    }

    public function __invoke(array $mention)
    {
        $mention_html = $this->fetchMentionHtml($mention['source']);
        $this->htmlContainsLink($mention_html, $mention['target']);
        return $mention_html;
    }

    private function fetchMentionHtml($url)
    {
        $this->log->info(sprintf("Fetching HTML from %s", $url));
        $result = $this->http->request("GET", $url);
        return (string) $result->getBody();
    }

    private function htmlContainsLink($html, $url)
    {
        $xpath = new \DOMXpath($this->loadDOM($html));
        foreach ($xpath->query('//a') as $link) {
            if ($link->getAttribute("href") == $url) {
                return true;
            }
        }
        throw new \Exception(sprintf("Source html does not contain link to target [%s]", $url));
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
}
