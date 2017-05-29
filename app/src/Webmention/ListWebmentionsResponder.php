<?php

namespace Aruna\Webmention;

use Aruna\Responder;

class ListWebmentionsResponder extends Responder
{
    /**
     * undocumented function
     *
     * @return void
     */
    public function __construct(
        $response,
        $twig,
        $purifier
    ) {
        $this->response = $response;
        $this->view = $twig;
        $this->purifier = $purifier;
    }

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
        $mention["body"] = $this->purifier->purify(
            $this->view->render(
                "mention-list-".$mention["type"].".html",
                array(
                    "mention" => $mention,
                    "author" => $mention["author"]
                )
            )
        );
        return $this->view->render(
            "mention-wrapper.html",
            array(
                "mention" => $mention,
                "author" => $mention["author"]
            )
        );
    }
}
