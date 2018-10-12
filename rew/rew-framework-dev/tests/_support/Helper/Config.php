<?php

namespace REW\Test\Helper;

use Codeception\Exception\ModuleConfigException;

class Config extends \Codeception\Module
{
    /**
     * Get module configuration
     * @param string|NULL $key
     * @throws ModuleConfigException
     * @return mixed|NULL
     */
    public function grabFromConfig($key = null)
    {
        $config = [];
        // every loaded module potentially has it's own config settings
        $mods = $this->getModules();
        foreach ($mods as $index => $mod) {
            $config = array_merge($config, $mod->_getConfig());
        }
        if (is_null($key)) {
            return $config;
        }
        if (isset($config[$key])) {
            return $config[$key];
        }

        throw new ModuleConfigException(get_class($this), sprintf(
            "Configuration setting \"%s\" not found.",
            $key
        ));
    }
}
