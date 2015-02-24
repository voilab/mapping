<?php

namespace voilab\mapping\plugin;

class FirstInCollection implements Plugin {

    /**
     * @inheritDocs
     */
    public function match ($key, $dottedKey) {
        return strpos($key, '[]') > 0;
    }

    /**
     * @inheritDocs
     */
    public function getData(\voilab\mapping\Mapping $mapping, $data, $key) {
        $key = str_replace('[]', '', $key);
        $relation = $mapping->getHydrator($data)->getRelation($data, $key);
        return $relation
            ? $this->getFirst($mapping->getHydrator($relation), $relation)
            : null;
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
