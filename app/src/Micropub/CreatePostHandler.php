<?php

namespace Aruna\Micropub;

use Aruna\Response\Unauthorized;
use Aruna\Response\ServerError;
use Aruna\Response\OK;

/**
 * Class CreatePostHandler
 * @author yourname
 */
class CreatePostHandler
{
    public function __construct(
        PostRepositoryWriter $postRepository,
        $accessToken
    ) {
        $this->postRepository = $postRepository;
        $this->accessTokenRepository = $accessToken;
    }

    public function handle(CreatePostCommand $command)
    {
        try {
            $response = $this->accessTokenRepository
                ->getTokenFromAuthCode($command->getAccessToken());
        } catch (\Exception $e) {
            $message = sprintf("Invalid access token [%s]", $e->getMessage());
            return new Unauthorized(["message" => $message]);
        }
        try {
            $post = new NewPost($command->getEntry(), $command->getFiles());
            $this->postRepository->save($post, $command->getFiles());
            return new OK(["post_uid" => $post->getUid(), "post_data" => $post->asJson()]);
        } catch (\Exception $e) {
            $message = sprintf("Failed to save new post [%s]", $e->getMessage());
            return new ServerError(["message" => $message]);
        }
    }
}
