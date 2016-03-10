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
        $from_id = ($request->query->get('from_id') !== null)
            ? $request->query->get('from_id')
            : 0;
        $posts = array_map(
            function ($post) use ($app) {
                return $this->createPostView($post, $app);
            },
            $this->postRepository->listFromId($from_id, $app['rpp'])
        );

        return $app['twig']->render(
            'feed.html',
            [
                'posts' => $posts
            ]
        );
    }

    public function getById(Application $app, $post_id)
    {
        $post = $this->createPostView(
            $this->postRepository->findById($post_id),
            $app
        );

        return $app['twig']->render(
            'post.html',
            [
                'post' => $post
            ]
        );
    }

    protected function createPostView($post, $app)
    {
        $published = new \DateTimeImmutable($post['published']);
        $url = $app['url_generator']->generate(
            'post',
            array('post_id' => $post['uid']),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $post['url'] = $url;
        $post['human_date'] = $published->format("Y-m-d");

        return $post;
    }
}
