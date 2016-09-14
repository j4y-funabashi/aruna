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
        $this->accessToken = $accessToken;
    }

    public function handle(CreatePostCommand $command)
    {
        try {
            $this->accessToken->getTokenFromAuthCode($command->getAccessToken());
        } catch (\Exception $e) {
            $message = sprintf("Invalid access token [%s]", $command->getAccessToken());
            return new Unauthorized(["message" => $message]);
        }
        try {
            $post = new NewPost($command->getEntry(), $command->getFiles());
            $this->postRepository->save($post, $command->getFiles());
            return new OK(["items" => [$post]]);
        } catch (\Exception $e) {
            $message = sprintf("Failed to save new post [%s]", $e->getMessage());
            return new ServerError(["message" => $message]);
        }
    }
}
