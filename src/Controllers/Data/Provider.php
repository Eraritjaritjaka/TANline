<?php

namespace TANline\Controllers\Data;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['controller.data'] = function() use($app) {
            $controller = new Controller($app['db.default']);
            $controller
                ->setRequest($app['request_stack'])
                ->setTwig($app['twig']);

            return $controller;
        };

        $controllers = $app['controllers_factory'];

        $controllers
            ->match('/', 'controller.data:indexAction')
            ->method('GET')
            ->bind('home');

        return $controllers;
    }
}
