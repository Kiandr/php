<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_IDX extends Backend_AuthCheck_App
{

    // Can Manage IDX
    public function manage()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_BUILDER'] &&
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Manage IDX Metadata
    public function meta()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_BUILDER'] && Settings::getInstance()->MODULES['REW_IDX_META_INFORMATION'] &&
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Manage IDX Metadata
    public function quicksearch()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_BUILDER'] && Settings::getInstance()->MODULES['REW_IDX_QUICKSEARCH'] &&
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Delete IDX Snippets
    public function delete_snippets()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS_DELETE)))
        ) ? true : false;
    }
}
