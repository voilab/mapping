<?php

namespace voilab\mapping\test\mock\data;

class StandardObjectData implements Data {

    public function getUser($nb) {
        return new User($nb);
    }

    public function getUsers() {
        return [
            $this->getUser(1),
            $this->getUser(2)
        ];
    }

}

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

class Group {
    private $id;
    private $name;

    public function __construct($id) {
        $this->id = $id;
        $this->name = 'group' . $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}

class Collection implements \IteratorAggregate, \ArrayAccess {

    private $data;

    public function offsetExists($offset) {
        return true;
    }

    public function offsetGet($offset) {
        return $this->data[0];
    }

    public function offsetSet($offset, $value) {
        return true;
    }

    public function offsetUnset($offset) {
        return true;
    }

    public function getIterator() {
        return new \ArrayIterator($this->data);
    }

    public function __construct($data) {
        $this->data = $data;
    }

}

class UserMagicCall extends User {

    public function __call($name, $args) {
        if (method_exists($this, $name) && is_callable([$this, $name])) {
            return $this->$name();
        } elseif (property_exists($this, $name) && isset($this->$name)) {
            return $this->$name;
        }
        throw new \Exception('Method or property [' . $name . "] doesn't exist");
    }

}