<?php

namespace REW\Theme\Enterprise;

use REW\Theme\Boilerplate\Installer as BoilerplateInstaller;

/**
 * @package REW\Theme\Enterprise
 */
class Installer extends BoilerplateInstaller
{

    /**
     * @return string
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return __DIR__;
    }
}
