<?php

namespace REW\Core\Asset\Manifest;

use REW\Core\Asset\Interfaces\ManifestInterface;

abstract class AbstractManifest implements ManifestInterface
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \BadMethodCallException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException(sprintf(
            '%s is read-only',
            get_class($this)
        ));
    }

    /**
     * @param mixed $offset
     * @throws \BadMethodCallException
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException(sprintf(
            '%s is read-only',
            get_class($this)
        ));
    }
}
