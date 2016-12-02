<?php

namespace Aruna\Micropub;

use Aruna\Response\OK;

class QueryHandler
{

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
            default:
                break;
        }
        return new OK(["body" => $out]);
    }

    private function getConfig()
    {
        return [
            "syndicate-to" => [
                "uid" => "https://media.j4y.co://brid.gy/publish/facebook",
                "name" => "Facebook (via brid.gy)"
            ]
        ];
    }
}
