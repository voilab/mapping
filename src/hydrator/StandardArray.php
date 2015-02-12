<?php

namespace voilab\mapping\hydrator;

class StandardArray implements Hydrator {

    /**
     * @inheritDocs
     */
    public function isTraversable($data) {
        return isset($data[0]) || !count($data);
    }

    /**
     * @inheritDocs
     */
    public function getFirst($data) {
        // check if array OR object, because an array-collection could
        // contain objects
        return isset($data[0]) && (is_object($data[0]) || is_array($data[0])) ? $data[0] : null;
    }

    /**
     * @inheritDocs
     */
    public function getRelation($data, $key) {
        return isset($data[$key]) && is_array($data[$key]) ? $data[$key] : null;
    }

    /**
     * @inheritDocs
     */
    public function getKeyContent($data, $key) {
        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * @inheritDocs
     */
    public function toArray($data) {
        return $data;
    }

}
