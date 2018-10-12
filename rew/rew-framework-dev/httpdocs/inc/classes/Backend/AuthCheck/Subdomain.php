<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Subdomain extends Backend_AuthCheck_App
{

    // Can Manage CMS Pages
    public function manage()
    {
        return ($this->auth->info('mode') == 'agent' && $this->auth->info('cms') == 'true')
            ? true : false;
    }

    // Can Manage ALL CMS Pages
    public function manage_all()
    {
        return $this->is_super_admin_as_admin() ? true : false;
    }

    // Can Manage CMS Snippets
    public function snippets()
    {
        return ($this->auth->info('mode') == 'agent' && $this->auth->info('cms') == 'true')
        ? true : false;
    }

    // Can Manage IDX Snippets
    public function idx()
    {
        $agent = Backend_Agent::load($this->auth->info('id'));
        return (Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'] &&
            $this->auth->info('mode') == 'agent' && $this->auth->info('cms') == 'true' &&
            \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->hasBackendIDXAccess())
            ? true : false;
    }

    // Can Conversion Tracking
    public function conversion_tracking()
    {
        return (Settings::getInstance()->MODULES['REW_CONVERSION_TRACKING'] &&
            ($this->auth->info('mode') == 'agent' && $this->auth->info('cms') == 'true')
        ) ? true : false;
    }

    // Can Manage Radio Landing Pages
    public function radio_landing_page()
    {
        return (Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'] &&
            ($this->auth->info('mode') == 'agent' && $this->auth->info('cms') == 'true')
        ) ? true : false;
    }
}
