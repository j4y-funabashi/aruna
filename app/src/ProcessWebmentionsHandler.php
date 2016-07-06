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
        // verify
        $mention = (new VerifyWebmentionRequest())->__invoke($mention);
        $mention_html = (new VerifyWebmention($this->log, $this->http))->__invoke($mention);

        $source_parts = parse_url($mention['source']);
        $source_base = $source_parts['scheme']."://".$source_parts['host'];
        $mention_view_model = new \Aruna\PostViewModel(\Mf2\parse($mention_html, $source_base));

        $post_id = basename($mention['target']);
        $this->saveData(
            $mention,
            $mention_html,
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

    private function saveData(
        $mention,
        $mention_html,
        $mention_view_model,
        $post_id
    ) {
        $mention_id = md5($mention['source'].$mention['target']);
        $this->saveHtml($mention, $mention_html, $mention_id);
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
