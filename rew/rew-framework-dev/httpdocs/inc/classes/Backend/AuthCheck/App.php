<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_App
{

    /**
     * Current Auth
     * @var Auth $auth
     */
    protected $auth;

    /**
     * @param Auth $auth Auth to be check against
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    // Is Super Admin in Admin Mode
    public function is_super_admin_as_admin()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ? true : false;
    }
}
