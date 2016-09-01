<?php

namespace Aruna\Micropub;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

use Aruna\Responder;

/**
 * Class CreatePostResponder
 * @author yourname
 */
class CreatePostResponder extends Responder
{
    public function postCreated($post)
    {
        $url = $this->urlGenerator->generate(
            'post',
            array('post_id' => $post->getPostId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $headers = ['Location' => $url];

        return new Response(json_encode($post), Response::HTTP_ACCEPTED, $headers);
    }

    public function unauthorized($message)
    {
        return new Response($message, Response::HTTP_UNAUTHORIZED);
    }
}
