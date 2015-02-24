<?php

namespace voilab\mapping\plugin;

class Relation implements Plugin {

    /**
     * @inheritDocs
     */
    public function match ($key, $dottedKey) {
        return true;
    }

    /**
     * @inheritDocs
     */
    public function getData(\voilab\mapping\Mapping $mapping, $data, $key) {
        return $mapping->getHydrator($data)->getRelation($data, $key);
    }

}
