<?php

namespace Mgp\PropertyBundle\Document;

/**
 * Pair
 *
 */
class Pair
{
    /**
     * Creates new instance
     *
     * @param string $key   Key
     * @param string $value Value
     */
    public function __construct($key, $value)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * Key
     *
     * @var string $key
     */
    private $key;

    /**
     * Value
     *
     * @var string $value
     */
    private $value;


    /**
     * Set key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}