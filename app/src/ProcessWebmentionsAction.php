<?php

namespace Aruna;

/**
 * Class ProcessWebmentionsAction
 * @author yourname
 */
class ProcessWebmentionsAction
{

    public function __construct(
        $log,
        $eventStore,
        $http,
        $mentionsRepositoryWriter,
        $postsRepositoryReader
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->http = $http;
        $this->mentionsRepositoryWriter = $mentionsRepositoryWriter;
        $this->postsRepositoryReader = $postsRepositoryReader;
    }

    public function __invoke()
    {
        $this->log->info("Processing webmentions");
        $count = 0;
        foreach ($this->eventStore->findByExtension('webmentions', 'json') as $mention_file) {

            $mention = json_decode($this->eventStore->readContents($mention_file['path']), true);
            try {
                $this->processWebmention($mention_file, $mention);
            } catch (\Exception $e) {
                $this->log->error(
                    sprintf("Invalid Webmention: %s\n", $e->getMessage()),
                    $mention
                );
            }

            $this->eventStore->delete($mention_file['path']);

            $count += 1;
            if ($count > 1) {
                exit;
            }
        }
    }

    private function processWebmention($mention_file, $mention)
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
        $m = $this->buildNotifyMessage(
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
        // save html
        $mention['id'] = md5($mention['source'].$mention['target']);
        $file_path = sprintf(
            "processed_webmentions/%s_%s_%s.html",
            $mention['id'],
            urlencode($mention['source']),
            urlencode($mention['target'])
        );
        $this->eventStore->save($file_path, $mention_html);
        // cache to db
        $this->mentionsRepositoryWriter->save(
            $mention['id'],
            $post_id,
            $mention_view_model
        );
    }

    private function buildNotifyMessage(
        $post_view_model,
        $mention_view_model
    ) {
        switch ($mention_view_model->type()) {
            case 'reply':
                $action = "commented on your";
                break;
            case 'like':
                $action = "liked your";
                break;
            default:
                $action = "linked to your";
                break;
        }
        return sprintf(
            '%s %s %s %s [%s][%s]',
            $mention_view_model->author()['name'],
            $action,
            $post_view_model->type(),
            $post_view_model->get("content")["value"],
            $post_view_model->get("url"),
            $mention_view_model->get("url")
        );
    }
}
