<?php

use REW\Backend\Partner\Moxiworks;

/**
 * Hook_REW_Moxiworks
 * Base class for Moxi Works hooks
 *
 * @package Hooks
 */
class Hook_REW_Moxiworks extends Hook
{

    /**
     * Get the Moxiworks partner instance (if available)
     * @return Moxiworks|NULL
     */
    protected function getPartner()
    {

        // Require add-on
        if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_MOXI_CRM'])) {
            return null;
        }

        // Require configured partner
        if (!($moxiworks = \Container::getInstance()->get(Moxiworks::class))) {
            return null;
        }

        return $moxiworks;
    }
}
