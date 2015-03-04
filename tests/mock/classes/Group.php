<?php

namespace voilab\mapping\test\mock\classes;

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