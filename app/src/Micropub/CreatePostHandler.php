<?php

namespace Aruna\Micropub;

use Aruna\Response\Unauthorized;
use Aruna\Response\ServerError;
use Aruna\Response\OK;
use Aruna\Response\Accepted;
use Aruna\Response\BadRequest;

/**
 * Class CreatePostHandler
 * @author yourname
 */
class CreatePostHandler
{
    public function __construct(
        $log,
        PostRepositoryWriter $postRepository,
        $accessToken,
        $queue
    ) {
        $this->log = $log;
        $this->postRepository = $postRepository;
        $this->accessTokenRepository = $accessToken;
        $this->queue = $queue;
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
            $files = $this->postRepository->saveMediaFiles($command->getFiles());
            $post = new NewPost(array_merge($command->getEntry(), $files));
            $this->postRepository->savePost($post);

            $m = sprintf("Created new event %s", $post->asJson());
            $this->log->info($m);

            $job_id = $this->queue->push(
                'micropub_events',
                $post->asJson()
            );

            $m = sprintf("Added event to queue [%s] %s", $job_id, $post->asJson());
            $this->log->info($m);

            if (isset($command->getEntry()["action"])) {
                return new OK([]);
            }
            return new Accepted(["post_uid" => $post->getUid(), "post_data" => $post->asJson()]);
        } catch (\InvalidArgumentException $e) {
            return new BadRequest([]);
        } catch (\Exception $e) {
            $message = sprintf("Failed to save new post [%s]", $e->getMessage());
            return new ServerError(["message" => $message]);
        }
    }
}
