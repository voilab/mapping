<?php

namespace voilab\mapping\test\plugin\firstInCollection;

use voilab\mapping\test\mock;

class StandardObjectTest extends MappingTestCase {

    protected $hydrator;

    public function setUp() {
        parent::setUp();
        $this->hydrator = new \voilab\mapping\hydrator\StandardObject;
        $this
            ->setMapping($this->hydrator)
            ->setData(new mock\StandardObjectData);
    }

}