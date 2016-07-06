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
        foreach ($this->eventStore->findByExtension('webmentions', 'json') as $mention_file) {
            $this->handleMention($mention_file);
            $count += 1;
            if ($count > 1) {
                exit;
            }
        }
    }

    private function handleMention($mention_file)
    {
        $mention = json_decode($this->eventStore->readContents($mention_file['path']), true);
        try {
            $this->handler->handle($mention_file, $mention);
        } catch (\Exception $e) {
            $this->log->error(
                sprintf("Invalid Webmention: %s\n", $e->getMessage()),
                $mention
            );
        }
        $this->eventStore->delete($mention_file['path']);
    }
}
