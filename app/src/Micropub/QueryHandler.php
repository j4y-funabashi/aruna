<?php

namespace Aruna\Micropub;

use Aruna\Response\OK;

class QueryHandler
{
    public function __construct(
        $postRepository,
        $filterProperties
    ) {
        $this->postRepository = $postRepository;
        $this->filterProperties = $filterProperties;
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
                $out = $this->getPostSource(
                    basename($command->get("url")),
                    (array) $command->get("properties")
                );
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
            ],
            "media-endpoint" => "https://j4y.co/micropub/media"
        ];
    }

    private function getPostSource($post_id, $properties)
    {
        return $this->filterProperties->__invoke(
            $this->postRepository->fetchDataById($post_id),
            $properties
        );
    }
}
