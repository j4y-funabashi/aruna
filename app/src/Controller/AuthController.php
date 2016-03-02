<?php

namespace Aruna\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

/**
 * Class AuthController
 * @author yourname
 */
class AuthController
{
    private $auth_url = "https://indieauth.com/auth";
    private $client_id = "http://127.0.0.1:4567";
    private $redirect_uri = "http://127.0.0.1:4567/auth";

    public function login(Application $app, Request $request)
    {
        return $app['twig']->render(
            'login.html',
            [
                'client_id' => $this->client_id,
                'redirect_uri' => $this->redirect_uri,
                'auth_url' => $this->auth_url
            ]
        );
    }

    public function auth(Application $app, Request $request)
    {
        $http = new Client();
        $response = $http->request(
            'POST',
            $this->auth_url,
            [
                'form_params' => [
                    'code' => $request->get('code'),
                        'redirect_uri' => $this->redirect_uri,
                        'auth_url' => $this->auth_url
                    ]
                ]
        );

        parse_str($response->getBody(), $body);
        $app['session']->set('user', $body['me']);
        return $app->redirect('/');
    }
}
