<?php

namespace Aruna\Micropub;

use Aruna\Response\OK;

class QueryHandler
{
    public function __construct(
        $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function handle($command)
    {
        $out = [];
        switch ($command->get("q")) {
            case 'config':
                $out = $this->getConfig();
                break;
            case 'syndicate-to':
                $out = $this->getConfig();
                break;
            case 'source':
                $out = $this->getPostSource(basename($command->get("url")));
                break;
            default:
                break;
        }
        return new OK(["body" => $out]);
    }

    private function getConfig()
    {
        return [
            "syndicate-to" => [
                [
                    "uid" => "https://brid.gy/publish/facebook",
                    "name" => "Facebook (via brid.gy)"
                ]
            ]
        ];
    }

    private function getPostSource($post_id)
    {
        return $this->postRepository->fetchDataById($post_id);
    }
}
