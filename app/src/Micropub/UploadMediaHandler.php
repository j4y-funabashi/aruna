<?php

namespace Aruna\Micropub;

use Aruna\Response\Unauthorized;
use Aruna\Response\ServerError;
use Aruna\Response\OK;

class UploadMediaHandler
{
    private $extractors = [];

    public function __construct(
        $log,
        PostRepositoryWriter $postRepository,
        $accessToken,
        $base_url,
        array $extractors
    ) {
        $this->log = $log;
        $this->postRepository = $postRepository;
        $this->accessTokenRepository = $accessToken;
        $this->base_url = $base_url;
        $this->extractors = $extractors;
    }

    public function handle($command)
    {
        try {
            $response = $this->accessTokenRepository
                ->getTokenFromAuthCode($command->getAccessToken());
        } catch (\Exception $e) {
            $message = sprintf("Invalid access token [%s]", $e->getMessage());
            return new Unauthorized(["message" => $message]);
        }
        try {
            $files = $this->postRepository->saveMediaFiles([[$command->getFile()]]);
            $out = [
                "location" => $this->base_url.$files[0][0],
                "body" => $this->getFileMetadata($command->getFile())
            ];
            return new OK($out);
        } catch (\Exception $e) {
            $message = sprintf("Failed to save media [%s]", $e->getMessage());
            return new ServerError(["message" => $message]);
        }
    }

    private function getFileMetadata($file)
    {
        $out = [];
        if (isset($this->extractors[mime_content_type($file->getRealPath())])) {
            $extractor = $this->extractors[mime_content_type($file->getRealPath())];
            $out = $extractor->__invoke($file);
        }
        return $out;
    }
}
