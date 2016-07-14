<?php

namespace Aruna;

/**
 * Class VerifyWebmentionRequest
 * @author yourname
 */
class VerifyWebmentionRequest
{

    public function __invoke(array $mention)
    {
        if (!isset($mention['source']) || !isset($mention['target'])) {
            $m = "Missing source or target";
            throw new \Exception($m);
        }
        if ($mention['source'] == $mention['target']) {
            $m = "source and target are the same url";
            throw new \Exception($m);
        }
        if (!$this->validateUrl($mention['source'])) {
            $m = sprintf("source url is not valid [%s]", $mention['source']);
            throw new \Exception($m);
        }
        if (!$this->validateUrl($mention['target'])) {
            $m = sprintf("target url is not valid [%s]", $mention['target']);
            throw new \Exception($m);
        }
        if ($this->validateTarget($mention['target'])) {
            $m = sprintf("target url is not valid [%s]", $mention['target']);
            throw new \Exception($m);
        }

        return $mention;
    }

    private function validateTarget($url)
    {
        $url = parse_url($url);
        if ($url['host'] == "j4y.co") {
            return false;
        }
        return true;
    }

    private function validateUrl($url)
    {
        $url = parse_url($url);
        if (!isset($url['scheme'])) {
            return false;
        }
        if (!isset($url['host'])) {
            return false;
        }
        if ($url['scheme'] != "http" && $url['scheme'] != "https") {
            return false;
        }
        return true;
    }
}
