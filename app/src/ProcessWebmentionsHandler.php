<?php

namespace Aruna;

class ProcessWebmentionsHandler
{
    public function __construct(
        $log,
        $eventStore,
        $http,
        $mentionsRepositoryWriter,
        $postsRepositoryReader,
        $mentionNotification,
        $notifyService
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->http = $http;
        $this->mentionsRepositoryWriter = $mentionsRepositoryWriter;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->mentionNotification = $mentionNotification;
        $this->notifyService = $notifyService;
    }

    public function handle($mention_file)
    {
        $mention = json_decode(
            $this->eventStore->readContents($mention_file['path']),
            true
        );
        $mention = $this->validate($mention);
        $mention_view_model = $this->getViewModel($mention);
        $post_id = basename($mention['target']);
        $this->saveData(
            $mention,
            $mention_view_model,
            $post_id
        );

        // homepage mention?
        $target_bits = parse_url($mention['target']);
        if (
            $target_bits['host'] == 'j4y.co'
            && (!isset($target_bits['path']) || $target_bits['path'] == '/')
        ) {
            $this->log->notice("HOMEPAGE MENTION", ["source" => $mention['source'], "target" => $mention['target']]);
            return;
        }
        $post_view_model = $this->postsRepositoryReader->findById($post_id);

        // notify
        $m = $this->mentionNotification->build(
            $post_view_model[0],
            $mention_view_model
        );

        $this->notifyService->notify($m);
    }

    private function getViewModel($mention)
    {
        $source_base = $this->getBaseUrl($mention['source']);
        return new \Aruna\PostViewModel(
            \Mf2\parse($mention['html'], $source_base)
        );
    }

    private function validate($mention)
    {
        $mention = (new VerifyWebmentionRequest())->__invoke($mention);
        $mention['html'] = (new VerifyWebmention($this->log, $this->http))->__invoke($mention);
        return $mention;
    }

    private function getBaseUrl($url)
    {
        $source_parts = parse_url($url);
        return $source_parts['scheme']."://".$source_parts['host'];
    }

    private function saveData(
        $mention,
        $mention_view_model,
        $post_id
    ) {
        $mention_id = md5($mention['source'].$mention['target']);
        $this->saveHtml($mention, $mention['html'], $mention_id);
        $this->mentionsRepositoryWriter->save(
            $mention_id,
            $post_id,
            $mention_view_model
        );
    }
    private function saveHtml(
        $mention,
        $mention_html,
        $mention_id
    ) {
        $file_path = sprintf("processed_webmentions/%s.html", $mention_id);
        $dom = $this->loadDOM($mention_html);
        $head = $dom->getElementsByTagName('head')->item(0);
        $head->appendChild(
            $this->createLink($dom, "aruna-webmention-source", $mention['source'])
        );
        $head->appendChild(
            $this->createLink($dom, "aruna-webmention-target", $mention['target'])
        );
        $this->eventStore->save($file_path, $dom->saveHtml());
    }
    private function createLink($dom, $rel, $href)
    {
        $link = $dom->createElement('link');
        $link->setAttribute("rel", $rel);
        $link->setAttribute("href", $href);
        return $link;
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
