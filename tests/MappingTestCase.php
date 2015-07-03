<?php

namespace voilab\mapping\test;

class MappingTestCase extends AbstractMappingTestCase {

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

    public function testStringDifferentKey() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                \voilab\mapping\Mapping::rel('id', 'data-id'),
                \voilab\mapping\Mapping::rel('login', 'login-name')
            ]
        );
        $this->assertSame([
            'data-id' => 1,
            'login-name' => 'login1'
        ], $result);
    }

    public function testCustomValues() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'id' => 1,
                'login' => 'custom',
                \voilab\mapping\Mapping::rel('id', 'data-id') => 'customId',
                'bool' => true,
                'json' => '{"id":1}'
            ]
        );
        $this->assertSame([
            'id' => 1,
            'login' => 'custom',
            'data-id' => 'customId',
            'bool' => true,
            'json' => '{"id":1}'
        ], $result);
    }

    public function testFunctionKey($idFunc, $loginFunc) {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                'login' => $loginFunc,
                \voilab\mapping\Mapping::rel('id', 'data-id') => $idFunc
            ]
        );
        $this->assertSame([
            'login' => 'login1-func',
            'data-id' => 2
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
                \voilab\mapping\Mapping::rel('mainGroup', 'group') => [
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
