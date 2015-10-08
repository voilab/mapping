<?php

namespace voilab\mapping\plugin;

use voilab\mapping\Mapping;

class Relation implements \voilab\mapping\Plugin {

    /**
     * @inheritDocs
     */
    public function match($key, $dottedKey) {
        return true;
    }

    /**
     * @inheritDocs
     */
    public function getData(Mapping $mapping, $data, $key) {
        return $mapping->getRelation($data, $key);
    }

}
