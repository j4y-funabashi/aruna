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
            "posts",
            "json",
            $rpp
        );
        foreach ($posts as $post) {
            $out_path = "events/".$post["basename"];
            try {
                if ($this->dataStore->exists($out_path)) {
                    $this->dataStore->delete($out_path);
                }
                $this->dataStore->save(
                    $out_path,
                    $this->convertPostToEvent($post)
                );
            } catch (\League\Flysystem\FileExistsException $e) {
                $m = sprintf("Skipping s3 write: Event exists %s", $out_path);
                $this->log->notice($m);
            }
        }
    }

    private function convertPostToEvent($post)
    {
        $post = json_decode(
            $this->dataStore->readContents($post["path"]),
            true
        );
        return json_encode(
            [
                "eventType" => $this->getEventType($post),
                "eventVersion" => $post["uid"],
                "eventID" => $post["uid"],
                "eventData" => $this->convertPostData($post)
            ]
        );
    }

    private function getEventType($post)
    {
        if (isset($post["action"]) && $post["action"] == "delete") {
            return "PostDeleted";
        }
        return "PostCreated";
    }

    private function convertPostData($post)
    {
        $post = $this->rmAccessToken($post);
        $post = $this->replaceFilesArray($post);
        $post = $this->convertHtoType($post);
        $post = $this->stringsToArrays($post);
        $post = $this->convertToMf2($post);
        return $post;
    }

    private function rmAccessToken($post)
    {
        unset($post["access_token"]);
        return $post;
    }

    private function replaceFilesArray($post)
    {
        if (isset($post["files"])) {
            foreach ($post["files"] as $k => $v) {
                $post[$k] = $v;
            }
            unset($post["files"]);
        }
        return $post;
    }

    private function convertHtoType($post)
    {
        $post["type"] = "h-".$post["h"];
        unset($post["h"]);
        return $post;
    }

    private function stringsToArrays($post)
    {
        foreach ($post as $k => $v) {
            if (!is_array($v)) {
                $post[$k] = array($v);
            }
        }
        return $post;
    }

    private function convertToMf2($post)
    {
        $type = $post["type"];
        unset($post["type"]);
        $out = ["type" => $type, "properties" => $post];
        return $out;
    }
}
