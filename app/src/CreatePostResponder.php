<?php

namespace Aruna;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreatePostResponder
 * @author yourname
 */
class CreatePostResponder
{
    public function __construct(
        $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    public function postCreated($post)
    {
        $url = $this->urlGenerator->generate(
            'post',
            array('post_id' => $post->getPostId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $headers = ['Location' => $url];

        return new Response($url, Response::HTTP_ACCEPTED, $headers);
    }

    public function unauthorized($message)
    {
        return new Response($message, Response::HTTP_UNAUTHORIZED);
    }
}
