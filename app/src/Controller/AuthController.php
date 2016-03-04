<?php

namespace Aruna\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use GuzzleHttp\Client;

/**
 * Class AuthController
 * @author yourname
 */
class AuthController
{
    private $auth_url = "https://indieauth.com/auth";

    public function __construct(
        $http,
        $log
    ) {
        $this->http = $http;
        $this->log = $log;
    }

    public function login(Application $app, Request $request)
    {
        return $app['twig']->render(
            'login.html',
            [
                'client_id' => $this->getClientId($app),
                'redirect_uri' => $this->getRedirectUri($app),
                'auth_url' => $this->auth_url
            ]
        );
    }

    private function getRedirectUri($app)
    {
        return $app['url_generator']->generate(
            'auth',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function getClientId($app)
    {
        return $app['url_generator']->generate(
            'root',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function auth(Application $app, Request $request)
    {
        $response = $this->http->request(
            'POST',
            $this->auth_url,
            [
                'form_params' => [
                    'code' => $request->get('code'),
                    'redirect_uri' => $this->getRedirectUri($app),
                    'auth_url' => $this->auth_url
                ]
            ]
        );

        parse_str($response->getBody(), $body);
        $app['session']->set('user', $body['me']);
        $this->log->info("Authenticated user: ".$body['me']);
        return $app->redirect('/');
    }
}
