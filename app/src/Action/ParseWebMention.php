<?php

namespace Aruna\Action;

/**
 * Class ParseWebMention
 * @author yourname
 */
class ParseWebMention
{
    public function __construct(
        $log,
        $eventStore
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
    }

    public function __invoke($mention)
    {
        $out_file = "processed_webmentions/".$mention['uid'].".json";

        if ($this->eventStore->exists($out_file)) {
            $mention = $this->eventStore->readContents($out_file);
            return $mention;
        }

        $m = sprintf(
            "Parsing mention [%s]",
            json_encode($mention)
        );
        $this->log->debug($m);

        if (false !== stripos($mention['target'], "http://j4y.co")) {
            $mention['target_uid'] = $this->getTargetUid($mention['target']);
            $http = new \GuzzleHttp\Client();
            $result = $http->get(
                $mention['source']
            );
            $source_body = trim($result->getBody());
            if (false !== stripos($source_body, $mention['target'])) {
                $mention['source_mf2_json'] = \Mf2\parse($source_body);
                $this->eventStore->save(
                    $out_file,
                    json_encode($mention)
                );
            }
        }

        return $mention;
    }

    private function getTargetUid($target_url)
    {
        return basename(parse_url($target_url, PHP_URL_PATH));
    }
}
