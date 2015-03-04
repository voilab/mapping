<?php

namespace voilab\mapping\test\mock;

class StandardObjectData implements Data {

    public function getUser($nb) {
        return new classes\User($nb);
    }

    public function getUsers() {
        return [
            $this->getUser(1),
            $this->getUser(2)
        ];
    }

}
