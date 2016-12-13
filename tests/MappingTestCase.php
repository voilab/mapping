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

    public function testFunctionIndex() {
        $special = [
            ['data' => 1],
            ['data' => 2]
        ];

        $result = $this->mapping->map($this->data->getUser(1), [
            'groups' => [[
                'name',
                'special' => function ($group, $index) use ($special) {
                    return $special[$index]['data'];
                }
            ]]
        ]);
        $this->assertSame([
            'groups' => [
                ['name' => 'group1', 'special' => 1],
                ['name' => 'group2', 'special' => 2]
            ]
        ], $result);
    }

    public function testFunctionParent() {
        $result = $this->mapping->map($this->data->getUser(1), [
            'groups' => [[
                'name',
                'contact' => [
                    'name' => function ($contact, $index, $indexes, $parents) {
                        $group_name = is_object($parents[1])
                            ? $parents[1]->getName()
                            : $parents[1]['name'];

                        return $contact['name'] . ' for ' . $group_name;
                    }
                ]
            ]]
        ]);
        $this->assertSame([
            'groups' => [
                ['name' => 'group1', 'contact' => ['name' => 'contact1 for group1']],
                ['name' => 'group2', 'contact' => ['name' => 'contact2 for group2']]
            ]
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
