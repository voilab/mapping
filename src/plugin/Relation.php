<?php

namespace voilab\mapping\plugin;

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
    public function getData(\voilab\mapping\Mapping $mapping, $data, $key) {
        return $mapping->getRelation($data, $key);
    }

}
