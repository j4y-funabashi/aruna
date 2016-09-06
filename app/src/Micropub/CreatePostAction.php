<?php

namespace Aruna\Micropub;

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
        $responder
    ) {
        $this->log = $logger;
        $this->handler = $handler;
        $this->responder = $responder;
    }

    public function __invoke(Request $request)
    {
        $this->responder->setPayload(
            $this->handler->handle($this->getCommand($request))
        );
        return $this->responder->__invoke();
    }

    private function getCommand($request)
    {
        return new CreatePostCommand(
            $entry = $this->buildEntryArray($request),
            $files = $this->buildFilesArray($request),
            $access_token = $this->getAccessToken($request)
        );
    }

    private function getAccessToken($request)
    {
        $body_token = $request->request->get('access_token');
        if ($request->getContentType() == "json") {
            $body_token = json_decode($request->getContent(), true)["access_token"];
        }
        return (null == $request->headers->get('Authorization'))
            ? $body_token
            : $request->headers->get('Authorization');
    }

    private function buildEntryArray($request)
    {
        $entry = [];
        foreach ($request->request->all() as $key => $value) {
            $entry[$key] = $value;
        }

        if ($request->getContentType() == "json") {
            return json_decode($request->getContent(), true);
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
        if ($uploadedFile->isReadable() !== true) {
            $m = "Could not read file ".$uploadedFile->getRealPath();
            throw new \RuntimeException($m);
        }
    }

    private function checkUploadIsValid($uploadedFile)
    {
        if (true !== $uploadedFile->isValid()) {
            throw new \RuntimeException("Upload Error: (".$uploadedFile->getError().")");
        }
    }
}
