<?php

namespace voilab\mapping\test\mock\classes;

class Collection implements \IteratorAggregate, \ArrayAccess {

    private $data;

    public function offsetExists($offset) {
        return true;
    }

    public function offsetGet($offset) {
        return $this->data[0];
    }

    public function offsetSet($offset, $value) {
        return true;
    }

    public function offsetUnset($offset) {
        return true;
    }

    public function getIterator() {
        return new \ArrayIterator($this->data);
    }

    public function __construct($data) {
        $this->data = $data;
    }

}