<?php

namespace voilab\mapping\hydrator;

class StandardObject implements Hydrator {

    /**
     * @inheritDocs
     */
    public function isTraversable($data) {
        return $data instanceof \Traversable;
    }

    /**
     * @inheritDocs
     */
    public function getFirst($data) {
        return $data instanceof \ArrayAccess ? $data->offsetGet(0) : null;
    }

    /**
     * @inheritDocs
     */
    public function getRelation($data, $key) {
        return $this->testObjectMethods($data, $key, [
            'get' . ucfirst($this->makeMethodName($key))
        ]);
    }

    /**
     * @inheritDocs
     */
    public function getKeyContent($data, $key) {
        $m = ucfirst($this->makeMethodName($key));
        return $this->testObjectMethods($data, $key, [
            'get' . $m,
            'is' . $m
        ]);
    }

    /**
     * @inheritDocs
     */
    public function toArray($data) {
        return (array) $data;
    }

    /**
     * Check which method exists inside this object and try to call them. This
     * method is ORM agnostic, since it just tries to call some common method
     * names on the object
     *
     * @param object $data
     * @param string $key
     * @param array $methods
     * @return mixed
     */
    protected function testObjectMethods($data, $key, $methods) {
        // check if direct property exists, in case it is public, which is not
        // a good idea by the way
        if (property_exists($data, $key) && isset($data->$key)) {
            return $data->$key;
        }
        // add plain key as a method name, in case it exists
        $methods[] = $key;
        $methods[] = $this->makeMethodName($key);
        foreach ($methods as $method) {
            // method_exists to be sure it exists in the object, and is_callable
            // to be sure it is public
            if (method_exists($data, $method) && is_callable([$data, $method])) {
                return $data->$method();
            }
        }
        // nothing exists
        return null;
    }

    /**
     * Create a method name based on an underscored string. For example:
     * my_method_name becomes myMethodName
     *
     * @param string $name
     * @return string
     */
    protected function makeMethodName($name) {
        return preg_replace_callback(
            '/(_[a-zA-Z])/',
            function ($m) {
                return substr(strtoupper($m[1]), 1);
            },
            $name
        );
    }

}
