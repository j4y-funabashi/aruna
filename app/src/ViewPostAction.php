<?php

namespace Aruna;

/**
 * Class ViewPostAction
 * @author yourname
 */
class ViewPostAction
{
    public function __construct(
        PostRepositoryReader $postRepository,
        MentionsRepositoryReader $mentionsRepository
    ) {
        $this->postRepository = $postRepository;
        $this->mentionsRepository = $mentionsRepository;
    }

    public function __invoke(Application $app, $post_id)
    {
        $post = new \Aruna\PostViewModel(
            $this->postRepository->findById($post_id),
            $app['url_generator']
        );
        $mentions = $this->mentionsRepository->findByPostId($post_id);

        return $app['twig']->render(
            'post.html',
            [
                'post' => $post,
                'mentions' => $mentions
            ]
        );
    }
}
