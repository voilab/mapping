<?php

namespace voilab\mapping\test;

use voilab\mapping\test\mock\data;

class StandardObjectTest extends MappingTestCase {

    protected $hydrator;

    public function setUp() {
        parent::setUp();
        $this->hydrator = new \voilab\mapping\hydrator\StandardObject;
        $this
            ->setMapping($this->hydrator)
            ->setData(new mock\data\StandardObjectData);
    }

    public function testFunctionKey() {
        parent::testFunctionKey(
            function (data\User $data) {
                return $this->functionId($data->getId());
            },
            function (data\User $data) {
                return $this->functionLogin($data->getLogin());
            }
        );
    }

    /**
     * @todo improve test to match mock objects, and not stdClass
     */
    public function testToArray() {
        // mock classes behave very strangely. This test should be improved
        // a lot
        $group = new \stdClass();
        $group->id = 1;
        $result = $this->hydrator->toArray($group);

        $this->assertSame([
            'id' => 1
        ], $result);
    }

    public function testIsTraversable() {
        $user = new data\User(1);
        $ok_full = new data\Collection([$user]);
        $ok_empty = new data\Collection([]);
        $ko = $user;

        $this->assertTrue($this->hydrator->isTraversable($ok_full), 'ok full');
        $this->assertTrue($this->hydrator->isTraversable($ok_empty), 'ok empty');
        $this->assertFalse($this->hydrator->isTraversable($ko), 'ko full');
    }

    public function testGetFirst() {
        $expected = new data\User(1);
        $test = new data\collection([
            $expected,
            new data\User(2)
        ]);
        $this->assertSame($expected, $this->hydrator->getFirst($test));
    }

    public function testGetRelation() {
        $user = new data\User(1);
        $this->assertSame($user->getMainGroup(), $this->hydrator->getRelation($user, 'mainGroup'));
        $this->assertNull($this->hydrator->getRelation($user, 'unavailable'));
    }

    public function testGetKeyContent() {
        $user = new data\User(1);
        $this->assertEquals(1, $this->hydrator->getKeyContent($user, 'id'));
        $this->assertNull($this->hydrator->getKeyContent($user, 'unavailable'));
    }

    public function testPublicPropertyKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'publicData'
            ]
        );
        $this->assertSame([
            'publicData' => 1
        ], $result);
    }

    public function testPrivatePropertyKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'privateData'
            ]
        );
        $this->assertSame([
            'privateData' => null
        ], $result);
    }

    public function testPrivateMethodKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'privateMethod'
            ]
        );
        $this->assertSame([
            'privateMethod' => null
        ], $result);
    }

    public function testCallablesKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'method_one',
                'method_two',
                'method_three'
            ]
        );
        $this->assertSame([
            'method_one' => 'm1',
            'method_two' => 'm2',
            'method_three' => 'm3'
        ], $result);
    }

}