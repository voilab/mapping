<?php

namespace voilab\mapping\test\mock\classes;

class Group {
    private $id;
    private $name;
    private $contact;

    public function __construct($id) {
        $this->id = $id;
        $this->name = 'group' . $id;
        $this->contact = [
            'name' => 'contact' . $id
        ];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getContact() {
        return $this->contact;
    }
}
