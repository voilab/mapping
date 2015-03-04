<?php

namespace voilab\mapping\test\mock;

class ArrayData implements Data {

    public function getUser($nb) {
        return [
            'id' => $nb,
            'login' => 'login' . $nb,
            'password' => 'pass' . $nb,
            'mainGroup' => [
                'id' => 'A',
                'name' => 'groupA'
            ],
            'groups' => [
                [
                    'id' => $nb,
                    'name' => 'group' . $nb
                ],
                [
                    'id' => $nb + 1,
                    'name' => 'group' . ($nb + 1)
                ]
            ]
        ];
    }

    public function getUsers() {
        return [
            $this->getUser(1),
            $this->getUser(2)
        ];
    }

}