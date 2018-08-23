<?php

class Loader {
    private $url;
    private $controller;
    private $action;
    private $namespace = "App\\Controller\\";

    public function __construct (){
        if (is_array($_GET)) {
            $this->url = $_GET;
        }

        if (isset($this->url['controller'])) {
            $this->controller = $this->namespace . $this->url['controller'];
        } else {
            $this->controller = $this->namespace . 'home';
        }

        if (isset($this->url['action'])) {
            $this->action = $this->url['action'];
        } else {
            $this->action = 'index';
        }
    }

    public function createController() {
        if (class_exists($this->controller)) {
            $parent = class_parents($this->controller);

            if (in_array($this->namespace . 'Controller', $parent)) {
                if(method_exists($this->controller, $this->action)) {
                    return new $this->controller($this->url, $this->action);
                } else {
                    throw new \Exception ("Method {$this->action} does not exists.");
                }
            } else {
                throw new \Exception("Controller of {$this->controller} not found.");
            }
        } else {
            throw new \Exception("Controller {$this->controller} not found.");
        }
    }
}