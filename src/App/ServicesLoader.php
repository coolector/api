<?php

namespace App;

use App\Controllers\UserController;
use Silex\Application;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use SimpleUser\UserServiceProvider;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        $this->app->register(new SecurityServiceProvider());
        $this->app->register(new UserServiceProvider());
        $this->app->register(new RememberMeServiceProvider());
        $this->app->register(new SessionServiceProvider());
        $this->app->register(new TwigServiceProvider());
        $this->app->register(new SwiftmailerServiceProvider());

        /**
         * Override user controller
         *
         * @param $app
         * @return UserController
         */
        $this->app['user.controller'] = function ($app) {
            $app['user.options.init']();

            $controller = new UserController($app['user.manager']);
            $controller->setUsernameRequired($app['user.options']['isUsernameRequired']);
            $controller->setEmailConfirmationRequired($app['user.options']['emailConfirmation']['required']);
            $controller->setTemplates($app['user.options']['templates']);
            $controller->setEditCustomFields($app['user.options']['editCustomFields']);

            return $controller;
        };

        $this->app['notes.service'] = function() {
            return new Services\NotesService($this->app["db"]);
        };

        $this->app['users'] = function () {
            return $this->app['user.manager'];
        };

        $this->app['security.firewalls'] = array(
            'login' => array(
                'pattern' => '^/user/login$',
            ),
            'secured' => array(
                'pattern' => '^.*$',
                'anonymous' => false,
                'remember_me' => array(),
                'form' => array(
                    'login_path' => '/user/login',
                    'check_path' => '/user/login_check',
                ),
                'logout' => array(
                    'logout_path' => '/user/logout',
                ),
                'users' => $this->app['users'],
            ),
        );
    }
}

