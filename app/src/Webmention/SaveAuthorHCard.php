<?php

namespace Aruna\Webmention;

use BarnabyWalters\Mf2;

class SaveAuthorHCard
{
    private $EXT_DIR = "ext/author_photo";

    public function __construct(
        $http,
        $fileStore
    ) {
        $this->http = $http;
        $this->fileStore =$fileStore;
    }

    public function __invoke($event)
    {
        if ($event["error"]) {
            return $event;
        }
        $hcard = Mf2\findMicroformatsByType($event["author"], 'h-card')[0];
        if (!Mf2\hasProp($hcard, "photo")) {
            return $event;
        }
        $photo = $this->downloadAuthorPhoto($hcard);
        $event["author"]["properties"]["photo"][0] = "/author_photo/".$photo["file"].$photo["ext"];
        return $event;
    }

    private function downloadAuthorPhoto($hcard)
    {
        $ext_photo_url = Mf2\getPlaintext($hcard, "photo");
        $result = $this->http->request("GET", $ext_photo_url);
        $out = [
            "data" => (string) $result->getBody(),
            "file" => md5($ext_photo_url),
            "ext" => $this->typeToExt($result->getHeader("Content-Type")[0]),
        ];
        $out["path"] = $this->EXT_DIR."/".$out["file"].$out["ext"];
        if (!$this->fileStore->exists($out["path"])) {
            $this->fileStore->save($out["path"], $out["data"]);
        }
        return $out;
    }

    private function typeToExt($type)
    {
        $map = array(
            'image/gif'         => '.gif',
            'image/jpeg'        => '.jpg',
            'image/png'         => '.png'
        );
        if (!isset($map[$type])) {
            return ".jpg";
        }
        return $map[$type];
    }
}
