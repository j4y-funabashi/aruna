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
        $mentionsRepositoryWriter
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->http = $http;
        $this->mentionsRepositoryWriter = $mentionsRepositoryWriter;
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
            if ($count > 10) {
                exit;
            }
        }
    }

    private function processWebmention($mention_file, $mention)
    {
        $mention = (new VerifyWebmentionRequest())->__invoke($mention);

        // verify
        $mention_html = (new VerifyWebmention($this->log, $this->http))->__invoke($mention);

        // save html
        $mention['id'] = md5($mention['source'].$mention['target']);
        $file_path = "processed_webmentions/".$mention['id'].".html";
        $this->eventStore->save($file_path, $mention_html);

        // cache to db
        $mention_view_model = new \Aruna\PostViewModel(\Mf2\parse($mention_html));
        $this->mentionsRepositoryWriter->save(
            $mention['id'],
            basename($mention['target']),
            $mention_view_model
        );

        // notify
        $m = sprintf(
            '%s %s %s: %s',
            $mention_view_model->author()['name'],
            $mention_view_model->type(),
            $mention['target'],
            $mention_view_model->get("url")
        );
        $this->log->notice($m);
    }
}
