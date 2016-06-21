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
        $postRenderer
    ) {
        $this->response = $response;
        $this->view = $view;
        $this->postRenderer = $postRenderer;
    }

    public function __invoke()
    {
        $class = get_class($this->payload);
        $method = isset($this->payload_method[$class])
            ? $this->payload_method[$class]
            : 'notRecognized';
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
