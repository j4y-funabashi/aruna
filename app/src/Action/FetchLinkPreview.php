<?php

namespace Aruna\Action;

/**
 * Class FetchLinkPreview
 * @author yourname
 */
class FetchLinkPreview
{

    public function __construct(
        $log,
        $linkPreview
    ) {
        $this->log = $log;
        $this->linkPreview = $linkPreview;
    }

    public function __invoke($event)
    {
        if (false === isset($event['bookmark-of'])) {
            return $event;
        }

        $event['link_preview'] = $this->getLinkPreview($event['bookmark-of']);

        return $event;
    }

    private function getLinkPreview($link_url)
    {
        $out = [];
        $parsed = $this->linkPreview->setUrl($link_url)
            ->getParsed();
        foreach ($parsed as $parserName => $link) {
            $out['url'] = $link->getRealUrl();
            $out['title'] = $link->getTitle();
            $out['image'] = $link->getImage();
        }

        return $out;
    }
}
