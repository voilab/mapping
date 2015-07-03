<?php

namespace voilab\mapping\test\plugin\relation;

use voilab\mapping\Hydrator;

class MappingTestCase extends \voilab\mapping\test\AbstractMappingTestCase {

    public function setMapping(Hydrator $objectHydrator = null, Hydrator $arrayHydrator = null) {
        parent::setMapping($objectHydrator, $arrayHydrator);
        // relation plugin is automatically added to the mapping plugins.
        // we keep this line commented so we can quickly see why these tests
        // are decoupled from the main testcase
        //$this->mapping->addPlugin(new \voilab\mapping\plugin\Relation());
        return $this;
    }

    public function testDottedKeyWithoutExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                \voilab\mapping\Mapping::rel('mainGroup.name', 'group-name')
            ]
        );
        $this->assertSame([
            'group-name' => 'groupA'
        ], $result);
    }

    public function testDottedKeyWithExistingMapping() {
        $result = $this->mapping->map(
            $this->data->getUser(1), [
                \voilab\mapping\Mapping::rel('mainGroup.name', 'group-name'),
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
                \voilab\mapping\Mapping::rel('mainGroup.test.name', 'group-name')
            ]
        );
        $this->assertSame([
            'group-name' => null
        ], $result);
    }

}
