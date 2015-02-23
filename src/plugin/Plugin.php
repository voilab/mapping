<?php

namespace voilab\mapping\plugin;

interface Plugin {

    /**
     * Test if the key contains the structure needed for this plugin. For
     * example, you can say that brackets (mykey[]) mean that you deal with
     * a collection.
     *
     * Default is a simple relation. So "mykey.field" will fetch a relation
     * named "mykey" and then test if it contains a "field" key
     *
     * @param string $key
     * @param string $dottedKey the complete dotted key
     * @return bool true if it matches, false if not
     */
    public function match($key, $dottedKey);

    /**
     * Method used to retrieve the desired dataset. For example, we could
     * want to fetch a collection-relation and fetch the last in the collection
     * or the first, or a random one.
     *
     * @param \voilab\mapping\plugin\Mapping $mapping
     * @param mixed $data array or object representing the main dataset
     * @param string $key the relation name
     * @return mixed the subset of the main dataset ($data)
     */
    public function getData(\voilab\mapping\Mapping $mapping, $data, $key);

}
