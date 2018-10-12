<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Settings extends Backend_AuthCheck_App
{

    // Can Manage Settings
    public function manage()
    {

        // Only Superadmin can edit the settings
        return $this->is_super_admin_as_admin();
    }
}
