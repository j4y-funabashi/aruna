<?php

namespace Aruna\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AuthController
 * @author yourname
 */
class AuthController
{

    public function login(Application $app, Request $request)
    {
        return $app['twig']->render(
            'login.html',
            [
                'client_id' => "http://127.0.0.1:4567",
                'redirect_uri' => "http://127.0.0.1:4567/auth"
            ]
        );
    }
}
