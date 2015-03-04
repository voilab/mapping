<?php

namespace voilab\mapping\test;

use voilab\mapping\Hydrator;

abstract class AbstractMappingTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * Data structure
     * @var mock\data\Data
     */
    protected $data;

    /**
     * Mapping object
     * @var \voilab\mapping\Mapping
     */
    protected $mapping;

    public function setMapping(Hydrator $objectHydrator = null, Hydrator $arrayHydrator = null) {
        $this->mapping = new \voilab\mapping\Mapping($objectHydrator, $arrayHydrator);
        return $this;
    }

    public function setData(mock\Data $data) {
        $this->data = $data;
        return $this;
    }

}
