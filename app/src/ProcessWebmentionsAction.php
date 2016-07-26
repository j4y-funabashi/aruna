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
        $handler
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->handler = $handler;
    }

    public function __invoke()
    {
        $count = 0;
        $limit = 10;

        $mention_files = array_slice(
            $this->eventStore->findByExtension('webmentions', 'json'),
            0,
            $limit
        );
        foreach ($mention_files as $mention_file) {
            $this->handleMention($mention_file);
            $this->eventStore->delete($mention_file['path']);
            $count += 1;
        }

        return array(
            "count" => $count
        );
    }

    private function handleMention($mention_file)
    {
        $mention = json_decode(
            $this->eventStore->readContents($mention_file['path']),
            true
        );
        try {
            $this->handler->handle($mention_file, $mention);
        } catch (\Exception $e) {
            $this->log->error(
                sprintf("Invalid Webmention: %s\n", $e->getMessage()),
                $mention
            );
        }
    }
}
