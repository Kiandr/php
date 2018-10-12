<?php

use REW\Core\Interfaces\Hook\CollectionInterface;

/**
 * Hook_Collection
 * @package Hooks
 */
class Hook_Collection implements CollectionInterface
{

    /**
     * Collection of hooks
     * @var Hook[]
     */
    protected $_hooks = array();

    /**
     * Create a new hook collection
     * @param Hook[] $hooks
     */
    public function __construct($hooks)
    {
        $this->_hooks = $hooks;
    }

    /**
     * Get the hooks in the collection
     * @return Hook[]
     */
    public function getHooks()
    {
        return $this->_hooks;
    }

    /**
     * Run all hooks in the collection
     */
    public function run()
    {

        // Run parameters
        $parameters = func_get_args();

        // Run hooks in collection
        $last_result = !empty($parameters[0]) ? $parameters[0] : null;
        foreach ($this->getHooks() as $hook) {
            // Use latest result
            if (!is_null($last_result) && !empty($parameters)) {
                $parameters[0] = $last_result;
            }

            try {
                // Execute next hook
                $hook_result = call_user_func_array(array($hook, 'run'), $parameters);
            } catch (Hook_Exception_Continue $e) {
                continue;

            // Stop running hooks
            } catch (Hook_Exception_Abort $e) {
                break;

            // Oh crap
            } catch (Exception $e) {
                throw $e;
            }

            // First parameter modified by hook
            if (!is_null($hook_result)) {
                $last_result = $hook_result;
            }
        }

        // Result from last hook
        return $last_result;
    }
}
