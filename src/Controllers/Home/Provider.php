<?php

namespace TANline\Controllers\Home;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['controller.home'] = function() use($app) {
            $controller = new Controller($app['db.default']);
            $controller
                ->setRequest($app['request_stack'])
                ->setTwig($app['twig']);

            return $controller;
        };

        $controllers = $app['controllers_factory'];

        $controllers
            ->match('/', 'controller.home:homeAction')
            ->method('GET')
            ->bind('home');

        $controllers
            ->match('/db/create', 'controller.home:dbCreate')
            ->method('GET')
            ->bind('db');

        $controllers
            ->match('/db/data', 'controller.home:dbData')
            ->method('GET')
            ->bind('db_data');

        return $controllers;
    }
}
