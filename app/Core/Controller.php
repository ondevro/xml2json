<?php

namespace App\Controller;

abstract class Controller {
    protected $url;
    protected $action;

    public function __construct($url, $action) {
        $this->url = $url;
        $this->action = $action;
    }

    public function action() {
        if (!empty($this->action)) {
            return $this->{$this->action}();
        }
    }

    public function view($name, $data = null) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . $name . '.php';

        if(file_exists($file)) {
            require_once($file);
        } else {
            throw new \Exception('View not found');
        }
    }
}