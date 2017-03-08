<?php

namespace App;

use App\Controllers\AuthenticationController;
use Silex\Application;
use SimpleUser\UserServiceProvider;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();

    }

    /**
     * Controllers
     */
    private function instantiateControllers()
    {
        $this->app['notes.controller'] = function() {
            return new Controllers\NotesController($this->app['notes.service']);
        };
        $this->app['authentication.controller'] = function() {
            return new AuthenticationController();
        };
    }

    /**
     * Binding routes of controllers
     */
    public function bindRoutesToControllers()
    {
        // Mount SimpleUser routes.
        $this->app->mount('/user', new UserServiceProvider());

        $api = $this->app["controllers_factory"];

        $api->get('/notes', "notes.controller:getAll");
        $api->get('/notes/{id}', "notes.controller:getOne");
        $api->post('/notes', "notes.controller:save");
        $api->put('/notes/{id}', "notes.controller:update");
        $api->delete('/notes/{id}', "notes.controller:delete");

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }
}

