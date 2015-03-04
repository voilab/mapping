<?php

namespace voilab\mapping\test\plugin\firstInCollection;

use voilab\mapping\test\mock;

class StandardArrayTest extends MappingTestCase {

    private $hydrator;

    public function setUp() {
        parent::setUp();
        $this->hydrator =  new \voilab\mapping\hydrator\StandardArray;
        $this
            ->setMapping(null, $this->hydrator)
            ->setData(new mock\ArrayData);
    }

    public function testNestedCollections() {
        $data = [
            'level' => 1,
            'items' => [
                0 => [
                    'level' => 2,
                    'tests' => [
                        0 => [
                            'level' => 3,
                            'item' => [
                                'level' => 4,
                                'ok' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $result = $this->mapping->map($data, [
            'deep-relation' => 'items[].tests[].item.ok'
        ]);
        $this->assertSame([
            'deep-relation' => true
        ], $result);
    }

}