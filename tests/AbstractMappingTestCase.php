<?php

namespace voilab\mapping\test;

use voilab\mapping\hydrator;

abstract class AbstractMappingTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * Some common mapping structures to test against hydrators
     * @var mock\map\Map
     */
    protected $map;

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

    public function setMap(mock\map\Map $map) {
        $this->map = $map;
        return $this;
    }

    public function setMapping(hydrator\Hydrator $objectHydrator = null, hydrator\Hydrator $arrayHydrator = null) {
        $this->mapping = new \voilab\mapping\Mapping($objectHydrator, $arrayHydrator);
        return $this;
    }

    public function setData(mock\data\Data $data) {
        $this->data = $data;
        return $this;
    }

}
