<?php

namespace REW\Backend\Interfaces;

interface SessionInterface
{

    /**
     * Writes and closes session
     * @return void
     */
    public function close();

    /**
     * Destroys session
     * @return boolean
     */
    public function destroy();

    /**
     * Get Session Value
     * @param string $index
     * @return mixed
     */
    public function get($index);

    /**
     * Set Session Value
     * @param string $index
     * @param mixed $value
     * @return void
     */
    public function set($index, $value);
}
