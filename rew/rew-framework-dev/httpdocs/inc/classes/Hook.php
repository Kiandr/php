<?php

use REW\Core\Interfaces\HookInterface;

/**
 * Hook
 * @package Hooks
 */
class Hook implements HookInterface
{

    /**
     * Hook name
     * @var string
     */
    protected $_name;

    /**
     * Hook priority
     * @var integer
     */
    protected $_priority;

    /**
     * Callable to run
     * @var callable
     */
    protected $_callable;

    /**
     * Create a new hook instance
     * @param string $name
     * @param integer $priority
     * @param callable|NULL $callable A callable to invoke when running the hook. Leave NULL to call 'invoke' method on Hook subclass instead.
     */
    public function __construct($name, $priority, $callable = null)
    {
        $this->_name = $name;
        $this->_priority = $priority;
        $this->_callable = $callable;
    }

    /**
     * Get the hook's name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the hook's priority
     * @return integer
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Run the defined implementation
     * @return mixed|NULL
     */
    public function run()
    {

        // Run parameters
        $parameters = func_get_args();

        // Run callable
        if (!empty($this->_callable) && is_callable($this->_callable)) {
            return call_user_func_array($this->_callable, $parameters);
        }

        // Invoke own implementation
        if (method_exists($this, 'invoke')) {
            return call_user_func_array(array($this, 'invoke'), $parameters);
        }

        throw new Exception(__METHOD__ . ' cannot run hook implementation for \'' . $this->getName() . '\' because no supported callable was specified');
    }
}
