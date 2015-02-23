<?php

namespace voilab\mapping;

class Mapping {

    /**
     * Used to change the relation's data accessor to an other string
     * @var string
     */
    const RELATION_KEY = '__accessor__';

    /**
     * Hydrator used to fetch data from an array structure
     * @var hydrator\Hydrator
     */
    private $arrayHydrator;

    /**
     * Hydrator used to fetch data from an object structure
     * @var hydrator\Hydrator
     */
    private $objectHydrator;

    /**
     * Constructor. Default hydrators are StandardObject and StandardArray.
     *
     * @param \voilab\mapping\hydrator\Hydrator $objectHydrator
     * @param \voilab\mapping\hydrator\Hydrator $arrayHydrator
     */
    public function __construct (hydrator\Hydrator $objectHydrator = null, hydrator\Hydrator $arrayHydrator = null) {
        $this
            ->setArrayHydrator($arrayHydrator ?: new hydrator\StandardArray())
            ->setObjectHydrator($objectHydrator ?: new hydrator\StandardObject());
    }

    /**
     * Set the array hydrator
     *
     * @param hydrator\Hydrator $hydrator
     * @return Mapping
     */
    public function setArrayHydrator(hydrator\Hydrator $hydrator) {
        $this->arrayHydrator = $hydrator;
        return $this;
    }

    /**
     * Set the object hydrator
     *
     * @param hydrator\Hydrator $hydrator
     * @return Mapping
     */
    public function setObjectHydrator(hydrator\Hydrator $hydrator) {
        $this->objectHydrator = $hydrator;
        return $this;
    }

    /**
     * Expose a set of data to a map, to filter content and have a constant
     * output
     *
     * Note: be aware that if you pass an object without mapping, the object
     * will be force-typed as array.
     *
     * @param array|object $body data as array or object, before any json
     * encoding
     * @param array $mapping a filter if we want specific fields only
     * @return array
     */
    public function map($body, array $mapping = null) {
        // if no mapping is set, force array, so an object is automatically
        // transformed.
        return $mapping ? $this->recursiveMap($body, $mapping) : $this->getHydrator($body)->toArray($body);
    }

    /**
     * Create the mapped data array
     *
     * @param array|object $data
     * @param array $mapping
     * @return array
     */
    private function recursiveMap($data, $mapping) {
        $map = [];
        if ($this->isCollection($mapping)) {
            if ($this->getHydrator($data)->isTraversable($data)) {
                foreach ($data as $o) {
                    $map[] = $this->recursiveMap($o, $mapping[0]);
                }
            }
        }
        elseif (is_array($data) || is_object($data)) {
            $use_wildcard = false;
            foreach ($mapping as $key => $m) {
                if ($m == '*') {
                    $use_wildcard = true;
                }
                // mapping is an array. Means that we want to check in a relation
                elseif (is_array($m)) {
                    $relkey = $key;
                    if (isset($m[self::RELATION_KEY])) {
                        $relkey = $m[self::RELATION_KEY];
                        unset($m[self::RELATION_KEY]);
                    }
                    $relation = $this->getHydrator($data)->getRelation($data, $relkey);
                    $map[$key] = $relation ? $this->recursiveMap($relation, $m) : (
                        // if relation is null, set an empty array for
                        // collections or null for toOne relations
                        $this->isCollection($m) ? [] : null
                    );
                }
                // key is int. That means we want the same key for mapping and
                // for the data array/obj.
                elseif (is_int($key)) {
                    $map[$m] = $this->getKeyContent($data, $m, $mapping);
                }
                // value is string. It's either a simple property of the
                // array/obj or it's a function to call in the object
                elseif (is_string($m)) {
                    $map[$key] = $this->getKeyContent($data, $m, $mapping);
                }
                // value is function. Call this function with data as first
                // argument
                elseif (is_callable($m)) {
                    $map[$key] = $m($data);
                }
            }
            // if value is a wildcard, fetch all fields with relations. Merge
            // with other mappings, if any.
            if ($use_wildcard) {
                // force array, so an object will be automatically transformed.
                // Objects will be transformed into an array automatically and
                // can behave strangely
                $map = array_merge($this->getHydrator($data)->toArray($data), $map);
            }
        }
        return $map;
    }

    /**
     * Check if the mapping block is a collection of data/object or is a simple
     * relation
     *
     * @param array $mapping
     * @return boolean
     */
    private function isCollection($mapping) {
        return $mapping && isset($mapping[0]) && is_array($mapping[0]) && count($mapping) == 1;
    }

    /**
     * Get best hydrator depending on data structure. Data can be array or
     * object, but can be both. For example, collections could be simple arrays
     * but each item could be object. We need for each action to be sure of
     * which hydrator to use.
     *
     * @param array|object $data
     * @return hydrator\Hydrator
     */
    private function getHydrator($data) {
        return is_array($data) ? $this->arrayHydrator : $this->objectHydrator;
    }

    /**
     * Return the content in the data array/object
     *
     * @param array|object $data
     * @param string $key
     * @param array $m
     * @return mixed
     */
    private function getKeyContent($data, $key, $m) {
        // if mapping has dots, we will look for the
        // deepest relation
        if (strpos($key, '.') !== false) {
            list($data, $key) = $this->getDataFromDottedKey($data, $key, $m);
            if (!$data) {
                return null;
            }
        }
        return $this->getHydrator($data)->getKeyContent($data, $key);
    }

    /**
     * Get key and data from a dotted key. Traverse mapping to find the right
     * data in the right relation
     *
     * @param array|object $data
     * @param string $key
     * @param array $m
     * @return array [$data, $key]
     */
    private function getDataFromDottedKey($data, $key, $m) {
        $tmp = explode('.', $key);
        // remove last element, which is the field name
        $key = array_pop($tmp);

        // traverse all relations to find in the end the
        // one having the field name
        while (count($tmp)) {
            $dkey = array_shift($tmp);
            $is_col = false;
            if (strpos($dkey, '[]')) {
                $dkey = str_replace('[]', '', $dkey);
                $is_col = true;
            }

            $data = $this->getHydrator($data)->getRelation($data, $dkey);

            $m = isset($m[$dkey]) ? $m[$dkey] : [];
            if ($is_col || $this->isCollection($m)) {
                $data = $this->getHydrator($data)->getFirst($data);
                $m = isset($m[0]) ? $m[0] : $m;
            }
            if (!$data) {
                return [null, null];
            }
        }
        return [$data, $key];
    }

}