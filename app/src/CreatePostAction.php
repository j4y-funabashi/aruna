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
        $access_token = (null == $request->headers->get('Authorization'))
            ? $request->request->get('access_token')
            : $request->headers->get('Authorization');
        $entry = $this->buildEntryArray($request);
        $files = $this->buildFilesArray($request);
        $command = new \Aruna\CreatePostCommand($entry, $files);

        try {
            $this->accessToken->getTokenFromAuthCode($access_token);
        } catch (\Exception $e) {
            return $this->responder->unauthorized($e->getMessage());
        }

        return $this->responder->postCreated(
            $this->handler->handle($command)
        );
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
            if (false === $uploadedFile->isValid()) {
                throw new \RuntimeException("Upload Error: (".$uploadedFile->getError().")");
            }
            $files[$file_key] = $uploadedFile;
        }
        return $files;
    }
}
