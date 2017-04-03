<?php

namespace Aruna\Webmention;

/**
 * Class VerifyWebmention
 * @author yourname
 */
class VerifyWebmention
{
    public function __invoke(array $mention)
    {
        $this->htmlContainsLink($mention["mention_source_html"], $mention['target']);
        return $mention;
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
