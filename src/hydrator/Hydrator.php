<?php

namespace voilab\mapping\hydrator;

interface Hydrator {

    /**
     * Check if the data block is a collection and can be foreachable
     *
     * @param array|object $data
     * @return boolean
     */
    public function isTraversable($data);

    /**
     * Get the related data
     *
     * @param array|object $data
     * @param string $key
     * @return array|object
     */
    public function getRelation($data, $key);

    /**
     * Return the content in data depending on the key
     *
     * @param array|object $data
     * @param string $key
     * @return mixed
     */
    public function getKeyContent($data, $key);

    /**
     * The mapping system returns an array in the end. This method is called
     * when no mapping is set or when wildcard is used
     *
     * @param array|object $data
     * @return array
     */
    public function toArray($data);

}
