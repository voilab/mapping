<?php

namespace voilab\mapping\plugin;

class FirstInCollection implements Plugin {

    /**
     * @inheritDocs
     */
    public function match ($key) {
        return strpos($key, '[]') > 0;
    }

    /**
     * @inheritDocs
     */
    public function getData(\voilab\mapping\Mapping $map, $data, $key) {
        $key = str_replace('[]', '', $key);
        $relation = $map->getHydrator($data)->getRelation($data, $key);
        return $relation
            ? $map->getHydrator($relation)->getFirst($relation)
            : null;
    }

    /**
     * @inheritDocs
     */
    public function setMap(array $map) {
        return isset($map[0]) ? $map[0] : $map;
    }
}
