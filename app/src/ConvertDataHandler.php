<?php

namespace Aruna;

class ConvertDataHandler
{
    public function __construct(
        $log,
        $dataStore
    ) {
        $this->log = $log;
        $this->dataStore = $dataStore;
    }

    public function handle($rpp)
    {
        $posts = $this->dataStore->findByExtension(
            "webmentions",
            "json",
            $rpp
        );
        foreach ($posts as $post_file) {
            $post_data = json_decode(
                $this->dataStore->readContents($post_file["path"]),
                true
            );
            $out_path = sprintf(
                "posts/%s/%s",
                (new \DateTimeImmutable($post_data["published"]))->format("Y"),
                $post_file["basename"]
            );
            try {
                $this->dataStore->save(
                    $out_path,
                    $this->convertPostToEvent($post_data)
                );
            } catch (\League\Flysystem\FileExistsException $e) {
                $m = sprintf("Skipping write: Event exists %s", $out_path);
                $this->log->notice($m);
            }
        }
    }

    private function convertPostToEvent($post)
    {
        return json_encode(
            [
                "eventType" => "WebmentionReceived",
                "eventVersion" => $post["uid"],
                "eventID" => $post["uid"],
                "eventData" => $post
            ]
        );
    }
}
