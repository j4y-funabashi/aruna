<?php

namespace Aruna\Micropub;

use Symfony\Component\HttpFoundation\Request;

class UploadMediaAction
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
        $config = [
            "file" => $this->getFile($request),
            "token" => $this->getAccessToken($request)
        ];

        return new UploadMediaCommand($config);
    }

    private function getFile($request)
    {
        $uploadedFile = $request->files->get("file");
        return new UploadedFile(
            $uploadedFile->getRealPath(),
            $uploadedFile->getClientOriginalExtension(),
            $uploadedFile->isReadable(),
            $uploadedFile->isValid()
        );
    }

    private function getAccessToken($request)
    {
        return (null == $request->headers->get('Authorization'))
            ? $request->request->get('access_token')
            : $request->headers->get('Authorization');
    }
}
