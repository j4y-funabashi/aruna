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
        $posts = array_map(
            function ($post) use ($app) {
                $published = new \DateTimeImmutable($post['published']);
                $url = $app['url_generator']->generate(
                    'post',
                    array('post_id' => $post['uid']),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $post['url'] = $url;
                $post['human_date'] = $published->format("d F Y");
                return $post;
            },
            $this->postRepository->listFromId($request->query->get('from_id'), 10)
        );

        return $app['twig']->render(
            'feed.html',
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
