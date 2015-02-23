<?php

namespace voilab\mapping\plugin;

class Relation implements Plugin {

    /**
     * @inheritDocs
     */
    public function match ($key) {
        return true;
    }

    /**
     * @inheritDocs
     */
    public function getData(\voilab\mapping\Mapping $map, $data, $key) {
        return $map->getHydrator($data)->getRelation($data, $key);
    }

    /**
     * @inheritDocs
     */
    public function setMap(array $map) {
        return $map;
    }
}
