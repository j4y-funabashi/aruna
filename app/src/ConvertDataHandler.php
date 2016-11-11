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
                $this->dataStore->save(
                    $out_path,
                    $this->convertPostData($post)
                );
            } catch (\League\Flysystem\FileExistsException $e) {
                $m = sprintf("Skipping s3 write: Event exists %s", $out_path);
                $this->log->notice($m);
            }
        }
    }

    private function convertPostData($post)
    {
        $post = json_decode(
            $this->dataStore->readContents($post["path"]),
            true
        );
        $post = $this->rmAccessToken($post);
        $post = $this->replaceFilesArray($post);
        $post = $this->convertHtoType($post);
        $post = $this->stringsToArrays($post);
        $post = $this->convertToMf2($post);
        return json_encode($post);
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
