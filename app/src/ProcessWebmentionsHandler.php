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
        $mentionNotification
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->http = $http;
        $this->mentionsRepositoryWriter = $mentionsRepositoryWriter;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->mentionNotification = $mentionNotification;
    }

    public function handle($mention_file, $mention)
    {
        $mention = $this->validate($mention);
        $mention_view_model = $this->getViewModel($mention);
        $post_id = basename($mention['target']);

        $this->saveData(
            $mention,
            $mention_view_model,
            $post_id
        );

        $post_view_model = $this->postsRepositoryReader->findById($post_id);

        // notify
        $m = $this->mentionNotification->build(
            $post_view_model[0],
            $mention_view_model
        );
        $this->log->notice($m);
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
        $file_path = sprintf(
            "processed_webmentions/%s_%s_%s.html",
            $mention_id,
            urlencode($mention['source']),
            urlencode($mention['target'])
        );
        $this->eventStore->save($file_path, $mention_html);
    }
}
