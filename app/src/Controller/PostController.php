<?php

namespace Aruna\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Aruna\PostRepositoryReader;
use Aruna\MentionsRepositoryReader;

/**
 * Class PostController
 * @author yourname
 */
class PostController
{
    public function __construct(
        PostRepositoryReader $postRepository,
        MentionsRepositoryReader $mentionsRepository
    ) {
        $this->postRepository = $postRepository;
        $this->mentionsRepository = $mentionsRepository;
    }

    public function getById(Application $app, $post_id)
    {
        $post = $this->postRepository->findById($post_id);
        $mentions = $this->mentionsRepository->findByPostId($post_id);
        $view_model = [
            'post' => new \Aruna\PostViewModel(
                $post['current'],
                $app['url_generator']
            ),
            'mentions' => $mentions,
        ];

        if (isset($post['next'])) {
            $view_model['next'] = new \Aruna\PostViewModel(
                $post['next'],
                $app['url_generator']
            );
        }
        if (isset($post['previous'])) {
            $view_model['previous'] = new \Aruna\PostViewModel(
                $post['previous'],
                $app['url_generator']
            );
        }

        return $app['twig']->render(
            'post.html',
            $view_model
        );
    }
}
