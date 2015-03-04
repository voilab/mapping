<?php

namespace voilab\mapping\test\plugin\firstInCollection;

use voilab\mapping\Hydrator;

class MappingTestCase extends \voilab\mapping\test\AbstractMappingTestCase {

    public function setMapping(Hydrator $objectHydrator = null, Hydrator $arrayHydrator = null) {
        parent::setMapping($objectHydrator, $arrayHydrator);
        $this->mapping->addPlugin(new \voilab\mapping\plugin\FirstInCollection());
        return $this;
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

}
