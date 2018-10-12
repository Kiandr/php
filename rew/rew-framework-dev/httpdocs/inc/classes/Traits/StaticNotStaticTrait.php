<?php

namespace REW\Traits;

use Container;

/**
 * Trait StaticNotStaticTrait
 * @package Traits
 *
 * This is a helper trait used to provide backwards compatibility for pre-DI code. This gets called from any method that
 * was executed statically but requires an instance.
 */
trait StaticNotStaticTrait
{
    /**
     * Gets the concrete instance for $abstract and executes a method on it, passing the provided arguments.
     * @param string $abstract
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    protected static function callInstanceMethod($abstract, $method, $arguments)
    {
        return call_user_func_array([Container::getInstance()->get($abstract), $method], $arguments);
    }
}
