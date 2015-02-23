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
            ? $this->getFirst($map->getHydrator($relation), $relation)
            : null;
    }

    /**
     * @inheritDocs
     */
    public function setMap(array $map) {
        return isset($map[0]) ? $map[0] : $map;
    }

    /**
     * Return first element of collection
     *
     * @param mixed $data the collection relation
     * @return mixed the first element in the collection
     */
    private function getFirst(\voilab\mapping\hydrator\Hydrator $hydrator, $data) {
        return $hydrator instanceof FirstInCollectionInterface
            ? $hydrator->getFirst($data)
            : null;
    }
}
