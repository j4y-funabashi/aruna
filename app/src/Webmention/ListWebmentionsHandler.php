<?php

namespace Aruna\Webmention;

use Aruna\Response\Found;

class ListWebmentionsHandler
{

    public function __construct(
        $mentionsRepo
    ) {
        $this->mentionsRepo = $mentionsRepo;
    }

    public function handle($command)
    {
        $mentions = $this->mentionsRepo->listMentions();
        $payload = ["items" => $mentions];
        return new Found($payload);
    }
}
