<?php

namespace Aruna\Pipeline;

/**
 * Class FetchLinkPreview
 * @author yourname
 */
class FetchLinkPreview
{

    public function __construct(
        $log,
        $linkPreview,
        $eventStore
    ) {
        $this->log = $log;
        $this->linkPreview = $linkPreview;
        $this->eventStore = $eventStore;
    }

    public function __invoke($event)
    {
        $valid_keys = [
            'bookmark-of',
            'like-of'
        ];

        foreach ($valid_keys as $key) {
            $event = $this->getLinkPreview($event, $key);
        }

        return $event;
    }

    private function getLinkPreview($event, $key)
    {
        if (false === isset($event[$key])) {
            return $event;
        }

        $out_file = "link_preview/".sha1($event[$key]).".json";
        if ($this->eventStore->exists($out_file)) {
            $link_preview = $this->eventStore->readContents($out_file);
            $event['link_preview'] = $link_preview;
            return $event;
        }

        $link_preview = $this->fetchLinkPreview($event[$key]);
        $event['link_preview'] = $link_preview;

        $this->eventStore->save(
            $out_file,
            json_encode($link_preview)
        );

        return $event;
    }

    private function fetchLinkPreview($link_url)
    {
        $out = [];
        try {
            $parsed = $this->linkPreview->setUrl($link_url)
                ->getParsed();
            foreach ($parsed as $parserName => $link) {
                $out = [
                    'url' => $link->getRealUrl(),
                    'title' => $link->getTitle(),
                    'image' => $link->getImage(),
                    'description' => $link->getDescription()
                ];
            }
        } catch (\Exception $e) {
            $out = [
                'url' => $link_url,
                'title' => $link_url,
                'image' => '',
                'description' => ''
            ];
        }

        return $out;
    }
}
