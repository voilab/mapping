<?php

namespace voilab\mapping\plugin;

class FirstInCollection implements \voilab\mapping\Plugin {

    /**
     * @inheritDocs
     */
    public function match($key, $dottedKey) {
        return strpos($key, '[]') > 0;
    }

    /**
     * @inheritDocs
     */
    public function getData(\voilab\mapping\Mapping $mapping, $data, $key) {
        $key = str_replace('[]', '', $key);
        list($relation, $hydrator) = $mapping->getRelationAndHydrator($data, $key);
        return $hydrator instanceof FirstInCollectionInterface && $relation
            ? $hydrator->getFirst($relation)
            : null;
    }

}
