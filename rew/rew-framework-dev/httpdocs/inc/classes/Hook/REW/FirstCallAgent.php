<?php

/**
 * Hook_REW_FirstCallAgent
 * Base class for Follow Up Boss hooks
 *
 * @package Hooks
 */
class Hook_REW_FirstCallAgent extends Hook
{

    /**
     * Get the FUB partner instance (if available)
     * @return FirstCallAgent|NULL
     */
    protected function getPartner()
    {

        // Require add-on
        if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_FIRSTCALLAGENT'])) {
            return null;
        }

        // Require configured partner
        if (!($firstcallagent = \Container::getInstance()->get(\REW\Backend\Partner\Firstcallagent::class))) {
            return null;
        }

        return $firstcallagent;
    }
}
