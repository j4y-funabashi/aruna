<?php

namespace Aruna\Controller;

use Silex\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Aruna\PostRepositoryReader;

/**
 * Class PostController
 * @author yourname
 */
class PostController
{
    public function __construct(
        PostRepositoryReader $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function feed(Request $request, Application $app)
    {
        $posts = $this->postRepository->listFromId($request->query->get('from_id'), 100);
        return $app['twig']->render(
            'feed.twig',
            [
                'posts' => $posts
            ]
        );
        return new JsonResponse(
            ['items' => $posts]
        );
    }

    public function getById(Application $app, $post_id)
    {
        $post = $this->postRepository->findById($post_id);
        return new JsonResponse(
            ["items" => $post]
        );
    }
}
