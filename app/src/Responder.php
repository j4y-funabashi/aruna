<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Responder
 * @author yourname
 */
abstract class Responder
{
    protected $payload_method = [];
    protected $payload;
    protected $response;
    protected $view;

    public function __construct(
        Response $response,
        $view,
        RenderPost $postRenderer
    ) {
        $this->response = $response;
        $this->view = $view;
        $this->postRenderer = $postRenderer;
    }

    public function __invoke()
    {
        $method = explode("\\", get_class($this->payload));
        $method = strtolower(array_pop($method));
        $this->$method();
        return $this->response;
    }

    public function setPayload(PayloadInterface $payload)
    {
        $this->payload = $payload;
    }

    protected function renderPost($post)
    {
        return $this->postRenderer->__invoke($post);
    }
}
