<?php

namespace voilab\mapping\test\mock\classes;

class User {

    private $id;
    private $login;
    private $password;
    private $mainGroup;
    private $groups;

    public $publicData;
    private $privateData;

    public function __construct($id) {
        $this->id = $id;
        $this->login = 'login' . $id;
        $this->password = 'pass' . $id;
        $this->mainGroup = new Group('A');
        $this->groups = new Collection([
            new Group($id),
            new Group($id + 1)
        ]);

        $this->publicData = $id;
        $this->privateData = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getMainGroup() {
        return $this->mainGroup;
    }

    public function getGroups() {
        return $this->groups;
    }

    public function methodOne() {
        return 'm1';
    }

    public function getMethodTwo() {
        return 'm2';
    }

    public function isMethodThree() {
        return 'm3';
    }

    private function getPrivateMethod() {
        return 'private';
    }
}