<?php

namespace Aruna;

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
        $form = $app['twig']->render(
            'login.html',
            [
                'client_id' => $this->getClientId($app),
                'redirect_uri' => $this->getRedirectUri($app),
                'auth_url' => $this->auth_url
            ]
        );

        return $app["twig"]->render(
            "page_wrapper.html",
            array(
                "page_title" => "login",
                "body" => $form
            )
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
        $auth_code = $request->get('code');

        $response = $this->http->request(
            'POST',
            $this->auth_url,
            [
                'form_params' => [
                    'code' => $auth_code,
                    'redirect_uri' => $this->getRedirectUri($app),
                    'auth_url' => $this->auth_url
                ]
            ]
        );
        parse_str($response->getBody(), $body);

        $response = $this->http->post(
            $app['token_endpoint'],
            [
                'form_params' => [
                    'me' => $body['me'],
                    'code' => $auth_code,
                    'redirect_uri' => $this->getRedirectUri($app),
                    'client_id' => $this->getClientId($app),
                    'scope' => 'post'
                ]
            ]
        );
        parse_str($response->getBody(), $body);

        $app['session']->set('me', $body['me']);
        $app['session']->set('access_token', $body['access_token']);
        $this->log->info("Authenticated user: ".$body['me']);
        return $app->redirect('/');
    }
}
