<?php

namespace voilab\mapping\test\mock\classes;

class UserMagicCall extends User {

    private $template;

    public function __construct($id) {
        parent::__construct($id);
        $this->template = new TemplateMagicCall;
    }

    public function __call($name, $args) {
        throw new \Exception('Method or property [' . $name . "] doesn't exist");
        if (method_exists($this, $name) && is_callable([$this, $name])) {
            return $this->$name();
        } elseif (property_exists($this, $name) && isset($this->$name)) {
            return $this->$name;
        } elseif (is_callable([$this->template, $name])) {
            return $this->template->$name();
        }
        throw new \Exception('Method or property [' . $name . "] doesn't exist");
    }

}

class TemplateMagicCall {

    public function getTemplate() {
        return 'template';
    }
}