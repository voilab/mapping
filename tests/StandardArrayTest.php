<?php

namespace voilab\mapping\test;

class StandardArrayTest extends MappingTestCase {

    private $hydrator;

    public function setUp() {
        parent::setUp();
        $this->hydrator =  new \voilab\mapping\hydrator\StandardArray;
        $this
            ->setMapping(null, $this->hydrator)
            ->setData(new mock\ArrayData);
    }

    public function testFunctionKey() {
        parent::testFunctionKey(
            function (array $data) {
                return $this->functionId($data['id']);
            },
            function (array $data) {
                return $this->functionLogin($data['login']);
            }
        );
    }

    public function testIsTraversable() {
        $ok_full = [[
            'id' => 1
        ]];
        $obj = new \stdClass();
        $obj->id = 1;
        $ok_object = [$obj];
        $ok_empty = [];
        $ko = [
            'id' => 1
        ];
        $this->assertTrue($this->hydrator->isTraversable($ok_full), 'ok full');
        $this->assertTrue($this->hydrator->isTraversable($ok_empty), 'ok empty');
        $this->assertTrue($this->hydrator->isTraversable($ok_object), 'ok object');
        $this->assertFalse($this->hydrator->isTraversable($ko), 'ko full');
    }

    public function testGetFirst() {
        $expected = ['id' => 1];
        $test = [
            $expected,
            ['id' => 2]
        ];
        $this->assertSame($expected, $this->hydrator->getFirst($test));
    }

    public function testGetRelation() {
        $expected = ['id' => 1];
        $test = [
            'id' => 2,
            'relation' => $expected
        ];
        $this->assertSame($expected, $this->hydrator->getRelation($test, 'relation'));
        $this->assertNull($this->hydrator->getRelation($test, 'id'));
    }

    public function testGetKeyContent() {
        $test = [
            'id' => 1
        ];
        $this->assertEquals(1, $this->hydrator->getKeyContent($test, 'id'));
        $this->assertNull($this->hydrator->getKeyContent($test, 'unavailable'));
    }

    public function testWildcardKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                '*'
            ]
        );
        $this->assertSame($this->data->getUser(1), $result);
    }

    public function testWildcardAndMappingKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                '*',
                'login' => function () {
                    return 'login-map';
                }
            ]
        );
        $data = array_merge($this->data->getUser(1), [
            'login' => 'login-map'
        ]);
        $this->assertSame($data, $result);
    }

}