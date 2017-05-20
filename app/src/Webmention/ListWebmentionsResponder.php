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
                "page_title" => "Notifications",
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
        $mention["published_human"] = (new \DateTimeImmutable($mention["published"]))->format("dS M, Y");
        return $this->view->render(
            "mention-list-".$mention["type"].".html",
            array(
                "mention" => $mention,
                "author" => $mention["author"]
            )
        );
    }
}
