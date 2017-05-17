<?php

namespace Aruna\Webmention;

use Aruna\Responder;

class ListWebmentionsResponder extends Responder
{

    public function found()
    {
        $out = $this->view->render(
            "page_wrapper.html",
            array(
                "page_title" => "mentions",
                "body" => $this->view->render(
                    "mentions-list.html",
                    ["mentions" => $this->renderMentions()]
                )
            )
        );
        $this->response->setContent($out);
    }

    private function renderMentions()
    {
        $this->payload->get("items");
        $out = [];
        foreach ($this->payload->get("items") as $mention) {
            $out[] = $this->renderMention($mention);
        }
        return implode("\n", $out);
    }

    private function renderMention($mention)
    {
        return $this->view->render(
            "mention-list-".$mention["type"].".html",
            array(
                "mention" => $mention,
                "author" => $mention["author"]
            )
        );
    }
}
