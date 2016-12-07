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
        $entry = $request->request->all();
        if ($request->getContentType() == "json") {
            $entry = json_decode($request->getContent(), true);
        }
        return new CreatePostCommand(
            $entry,
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
        $f = array_map(
            function ($files) {
                if (is_array($files)) {
                    return $files;
                }
                return [$files];
            },
            $request->files->all()
        );
        foreach ($f as $file_key => $uploadedFiles) {
            array_walk_recursive(
                $uploadedFiles,
                function ($uploadedFile) use (&$out, $file_key) {
                    $out[$file_key][] = new UploadedFile(
                        $uploadedFile->getRealPath(),
                        $uploadedFile->getClientOriginalExtension(),
                        $uploadedFile->isReadable(),
                        $uploadedFile->isValid()
                    );
                }
            );
        }
        return $out;
    }
}
