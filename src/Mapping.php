<?php

namespace voilab\mapping;

class Mapping {

    /**
     * Used to change the relation's data accessor to an other string
     * @var string
     */
    const RELATION_ALIAS = ' as ';

    /**
     * The separator used in the mapping key that triggers plugins operations
     * @var string
     */
    private $pluginKeySeparator = '.';

    /**
     * Hydrator used to fetch data from an array structure
     * @var Hydrator
     */
    private $arrayHydrator;

    /**
     * Hydrator used to fetch data from an object structure
     * @var Hydrator
     */
    private $objectHydrator;

    /**
     * Plugins used to manage dotted-keys
     * @var Plugin[]
     */
    private $plugins = [];

    /**
     * Get the key string for an alias to a relation
     *
     * @param string $relation the relation key
     * @param string $alias the relation alias
     * @return string
     */
    public static function rel($relation, $alias) {
        return $relation . self::RELATION_ALIAS . $alias;
    }

    /**
     * Constructor. Default hydrators are StandardObject and StandardArray.
     *
     * @param Hydrator $objectHydrator
     * @param Hydrator $arrayHydrator
     */
    public function __construct (Hydrator $objectHydrator = null, Hydrator $arrayHydrator = null) {
        $this
            ->addPlugin(new plugin\Relation())
            ->setArrayHydrator($arrayHydrator ?: new hydrator\StandardArray())
            ->setObjectHydrator($objectHydrator ?: new hydrator\StandardObject());
    }

    /**
     * Set the separator used in the mapping key that triggers plugins
     * operations
     *
     * @param string $separator can be multiple chars (used in explode())
     * @return Mapping
     */
    public function setPluginKeySeparator($separator) {
        $this->pluginKeySeparator = $separator;
        return $this;
    }

    /**
     * Add a plugin for dotted-key management
     *
     * @param Plugin $plugin
     * @return Mapping
     */
    public function addPlugin(Plugin $plugin) {
        array_unshift($this->plugins, $plugin);
        return $this;
    }

    /**
     * Set the array hydrator
     *
     * @param Hydrator $hydrator
     * @return Mapping
     */
    public function setArrayHydrator(Hydrator $hydrator) {
        $this->arrayHydrator = $hydrator;
        return $this;
    }

    /**
     * Set the object hydrator
     *
     * @param Hydrator $hydrator
     * @return Mapping
     */
    public function setObjectHydrator(Hydrator $hydrator) {
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
        return $mapping
            ? $this->recursiveMap($body, $mapping)
            : $this->getHydrator($body)->toArray($body);
    }

    /**
     * Get best hydrator depending on data structure. Data can be array or
     * object, but can be both. For example, collections could be simple arrays
     * but each item could be object. We need for each action to be sure of
     * which hydrator to use.
     *
     * @param array|object $data
     * @return Hydrator
     */
    public function getHydrator($data) {
        return is_array($data) ? $this->arrayHydrator : $this->objectHydrator;
    }

    /**
     * Get the related dataset (array or object)
     *
     * @param array|object $data
     * @param string $key
     * @return array|object
     */
    public function getRelation($data, $key) {
        return $this->getHydrator($data)->getRelation($data, $key);
    }

    /**
     * Get the related dataset (array or object) and the hydrator that depends
     * to the new dataset
     *
     * @param array|object $data
     * @param string $key
     * @return [array|object, Hydrator]
     */
    public function getRelationAndHydrator($data, $key) {
        $relation = $this->getRelation($data, $key);
        return [
            $relation,
            $this->getHydrator($relation)
        ];
    }

    /**
     * Create the mapped data array
     *
     * @param array|object $data
     * @param array $mapping
     * @param mixed $index the index key when in a loop
     * @param array $indexes tree of current indexes if multiple loops
     * @return array
     */
    private function recursiveMap($data, $mapping, $index = null, array $indexes = []) {
        $map = [];
        if ($this->isCollection($mapping)) {
            if ($this->getHydrator($data)->isTraversable($data)) {
                foreach ($data as $i => $o) {
                    $indexes[] = $i;
                    $map[] = $this->recursiveMap($o, $mapping[0], $i, $indexes);
                }
            }
        }
        elseif (is_array($data) || is_object($data)) {
            $use_wildcard = false;
            foreach ($mapping as $key => $m) {
                if ($m === '*') {
                    $use_wildcard = true;
                }
                // mapping is an array. Means that we want to check in a
                // relation
                elseif (is_array($m)) {
                    $relkey = $key;
                    if (strpos($key, self::RELATION_ALIAS)) {
                        $tmp = explode(self::RELATION_ALIAS, $key);
                        $key = trim($tmp[1]);
                        $relkey = trim($tmp[0]);
                    }
                    $relation = $this->getRelation($data, $relkey);
                    $map[$key] = $relation ? $this->recursiveMap($relation, $m) : (
                        // if relation is null, set an empty array for
                        // collections or null for toOne relations
                        $this->isCollection($m) ? [] : null
                    );
                }
                // key is int. That means we want the same key for mapping and
                // for the data array/obj.
                elseif (is_int($key)) {
                    $map[$m] = $this->getKeyContent($data, $m);
                }
                // value is string. It's either a simple property of the
                // array/obj or it's a function to call in the object
                elseif (is_string($m)) {
                    $map[$key] = $this->getKeyContent($data, $m);
                }
                // value is function. Call this function with data as first
                // argument
                elseif (is_callable($m)) {
                    $map[$key] = $m($data, $index, $indexes);
                }
            }
            // if value is a wildcard, fetch all fields with relations. Merge
            // with other mappings, if any.
            if ($use_wildcard) {
                // force array, so an object will be automatically transformed.
                // Objects will be transformed into an array automatically and
                // can behave strangely
                $map = array_merge($this->getHydrator($data)
                    ->toArray($data), $map);
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
    private function isCollection(array $mapping) {
        return isset($mapping[0])
            && is_array($mapping[0])
            && count($mapping) === 1;
    }

    /**
     * Return the content in the data array/object
     *
     * @param array|object $data
     * @param string $key
     * @return mixed
     */
    private function getKeyContent($data, $key) {
        // if mapping has dots, we will look for the
        // deepest relation
        if ($this->pluginKeySeparator && strpos($key, $this->pluginKeySeparator) !== false) {
            list($data, $key) = $this->getDataFromPlugins($data, $key);
            if (!$data) {
                return null;
            }
        }
        return $this->getHydrator($data)->getKeyContent($data, $key);
    }

    /**
     * Get key and data from dotted (or any user defined string) key. Traverse
     * mapping to find the right data in the right relation
     *
     * @param array|object $data
     * @param string $key
     * @return array [$data, $key]
     */
    private function getDataFromPlugins($data, $key) {
        $tmp = explode($this->pluginKeySeparator, $key);
        // remove last element, which is the field name
        $last_key = array_pop($tmp);
        // traverse relations to find in the end the one having the field name
        while (count($tmp)) {
            $dkey = array_shift($tmp);
            foreach ($this->plugins as $plugin) {
                if ($plugin->match($dkey, $key)) {
                    $data = $plugin->getData($this, $data, $dkey);
                    if ($data) {
                        // relation is found, continue to the next $dkey inside
                        // the while loop
                        break 1;
                    } else {
                        // relation is not found, quit foreach and while loops
                        // and return a $data with null value
                        break 2;
                    }
                }
            }
        }
        return [$data, $last_key];
    }

}
