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

        $mention_files = $this->eventStore->findByExtension('webmentions', 'json', $limit);
        foreach ($mention_files as $mention_file) {
            try {
                $this->handler->handle($mention_file);
            } catch (\Exception $e) {
                $this->log->error(
                    sprintf("Failed to process webmention: [%s] %s\n", get_class($e), $e->getMessage())
                );
            }
            $this->eventStore->delete($mention_file['path']);
            $count += 1;
        }

        return array(
            "count" => $count
        );
    }
}
