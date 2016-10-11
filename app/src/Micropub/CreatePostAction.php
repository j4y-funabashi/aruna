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
        $handler,
        $responder
    ) {
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
            $entry = $request->request->all(),
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

    private function buildFilesArray($request)
    {
        $files = [];
        foreach ($request->files as $file_key => $uploadedFile) {
            $files[$file_key] = new UploadedFile(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalExtension(),
                $uploadedFile->isReadable(),
                $uploadedFile->isValid()
            );
        }
        return $files;
    }
}
