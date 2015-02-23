<?php

namespace voilab\mapping\test;

use voilab\mapping\hydrator;

class MappingTestCase extends \PHPUnit_Framework_TestCase {

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
        $this->mapping->addPlugin(new \voilab\mapping\plugin\FirstInCollection());
        return $this;
    }

    public function setData(mock\data\Data $data) {
        $this->data = $data;
        return $this;
    }

    public function testNullData() {
        $this->assertSame([], $this->mapping->map(null, [
            'id',
            'login'
        ]));
    }

    public function testNullCollectionData() {
        $this->assertSame([], $this->mapping->map(null, [[
            'id',
            'login'
        ]]));
    }

    public function testIntKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'id',
                'login'
            ]
        );
        $this->assertSame([
            'id' => 1,
            'login' => 'login1'
        ], $result);
    }

    public function testStringSameKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'id' => 'id',
                'login' => 'login'
            ]
        );
        $this->assertSame([
            'id' => 1,
            'login' => 'login1'
        ], $result);
    }

    public function testStringDifferentKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'data-id' => 'id',
                'login-name' => 'login'
            ]
        );
        $this->assertSame([
            'data-id' => 1,
            'login-name' => 'login1'
        ], $result);
    }

    public function testFunctionKey($idFunc, $loginFunc) {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'id' => $idFunc,
                'login' => $loginFunc
            ]
        );
        $this->assertSame([
            'id' => 2,
            'login' => 'login1-func'
        ], $result);
    }

    protected function functionId($id) {
        return (int) $id + 1;
    }

    protected function functionLogin($login) {
        return $login . '-func';
    }

    public function testRelation() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'mainGroup' => [
                    'id',
                    'name'
                ]
            ]
        );
        $this->assertSame([
            'mainGroup' => [
                'id' => 'A',
                'name' => 'groupA'
            ]
        ], $result);
    }

    public function testRelationOtherKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group' => [
                    \voilab\mapping\Mapping::RELATION_KEY => 'mainGroup',
                    'id',
                    'name'
                ]
            ]
        );
        $this->assertSame([
            'group' => [
                'id' => 'A',
                'name' => 'groupA'
            ]
        ], $result);
    }

    public function testDottedKeyWithoutExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'mainGroup.name'
            ]
        );
        $this->assertSame([
            'group-name' => 'groupA'
        ], $result);
    }

    public function testDottedKeyWithExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'mainGroup.name',
                'mainGroup' => [
                    'name'
                ]
            ]
        );
        $this->assertSame([
            'group-name' => 'groupA',
            'mainGroup' => [
                'name' => 'groupA'
            ]
        ], $result);
    }

    public function testNoDottedKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'mainGroup.test.name'
            ]
        );
        $this->assertSame([
            'group-name' => null
        ], $result);
    }

    public function testDottedCollectionKeyWithoutExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'groups[].name'
            ]
        );
        $this->assertSame([
            'group-name' => 'group1'
        ], $result);
    }

    public function testDottedCollectionKeyWithExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'groups[].name',
                'groups' => [[
                    'name'
                ]]
            ]
        );
        $this->assertSame([
            'group-name' => 'group1',
            'groups' => [
                ['name' => 'group1'],
                ['name' => 'group2']
            ]
        ], $result);
    }

    public function testNoDottedCollectionKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'group-name' => 'groups[].test.name'
            ]
        );
        $this->assertSame([
            'group-name' => null
        ], $result);
    }

    public function testCollectionKey() {
        $result = $this->mapping->map(
            $this->data->getUsers(), [[
                'id',
                'login'
            ]]
        );
        $this->assertSame([
            ['id' => 1, 'login' => 'login1'],
            ['id' => 2, 'login' => 'login2']
        ], $result);
    }

    public function testNoKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'unavailable-key'
            ]
        );
        $this->assertSame([
            'unavailable-key' => null
        ], $result);
    }

    public function testNoRelationKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'unavailable-relation' => [
                    'id',
                    'name'
                ]
            ]
        );
        $this->assertSame([
            'unavailable-relation' => null
        ], $result);
    }

    public function testNoRelationCollectionKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'unavailable-relation' => [[
                    'id',
                    'name'
                ]]
            ]
        );
        $this->assertSame([
            'unavailable-relation' => []
        ], $result);
    }

}
