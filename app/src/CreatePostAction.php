<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreatePostAction
 * @author yourname
 */
class CreatePostAction
{

    public function __construct(
        $logger,
        $handler,
        $accessToken,
        $responder
    ) {
        $this->log = $logger;
        $this->handler = $handler;
        $this->accessToken = $accessToken;
        $this->responder = $responder;
    }

    public function __invoke(Request $request)
    {
        $command = $this->getCommand($request);

        try {
            $this->accessToken->getTokenFromAuthCode($this->getAccessToken($request));
        } catch (\Exception $e) {
            return $this->responder->unauthorized($e->getMessage());
        }

        return $this->responder->postCreated(
            $this->handler->handle($command)
        );
    }

    private function getCommand($request)
    {
        return new \Aruna\CreatePostCommand(
            $entry = $this->buildEntryArray($request),
            $files = $this->buildFilesArray($request),
            $access_token = $this->getAccessToken($request)
        );
    }

    private function getAccessToken($request)
    {
        return (null == $request->headers->get('Authorization'))
            ? $request->request->get('access_token')
            : $request->headers->get('Authorization');
    }

    private function buildEntryArray($request)
    {
        $entry = [];
        foreach ($request->request->all() as $key => $value) {
            $entry[$key] = $value;
        }
        return $entry;
    }

    private function buildFilesArray($request)
    {
        $files = [];
        foreach ($request->files as $file_key => $uploadedFile) {
            $this->validateFile($uploadedFile);
            $files[$file_key] = [
                'real_path' => $uploadedFile->getRealPath(),
                    'original_ext' => $uploadedFile->getClientOriginalExtension()
                ];
        }

        return $files;
    }

    private function validateFile($uploadedFile)
    {
        $this->checkUploadIsValid($uploadedFile);
        $this->checkUploadIsReadable($uploadedFile);
    }

    private function checkUploadIsReadable($uploadedFile)
    {
        if ($uploadedFile->isReadable() === false) {
            $m = "Could not read file ".$uploadedFile->getRealPath();
            throw new \RuntimeException($m);
        }
    }

    private function checkUploadIsValid($uploadedFile)
    {
        if (false === $uploadedFile->isValid()) {
            throw new \RuntimeException("Upload Error: (".$uploadedFile->getError().")");
        }
    }
}
