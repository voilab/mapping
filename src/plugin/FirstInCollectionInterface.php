<?php

namespace voilab\mapping\plugin;

/**
 * Use this interface in hydrator, so you can add the ability to behave the way
 * this plugin wants hydrators to behave.
 */
interface FirstInCollectionInterface {

    /**
     * Get first item in a collection. This method is always called with a
     * data that is explicitly a collection
     *
     * @param mixed $data
     * @return array|object
     */
    public function getFirst($data);

}
