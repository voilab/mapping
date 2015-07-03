<?php

namespace voilab\mapping\test\plugin\relation;

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

    public function testNestedRelations() {
        $data = [
            'level' => 1,
            'item' => [
                'level' => 2,
                'test' => [
                    'level' => 3,
                    'item' => [
                        'level' => 4,
                        'ok' => true
                    ]
                ]
            ]
        ];
        $result = $this->mapping->map($data, [
            \voilab\mapping\Mapping::rel('item.test.item.ok', 'deep-relation')
        ]);
        $this->assertSame([
            'deep-relation' => true
        ], $result);
    }

}