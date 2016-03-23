<?php

namespace Aruna\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

/**
 * Class MicropubController
 * @author yourname
 */
class MicropubController
{

    public function form(Application $app, Request $request)
    {
        return $app['twig']->render(
            'micropub.html',
            [
                'current_date' => date('c'),
                'access_token' => $app['session']->get('access_token')
            ]
        );
    }
}
