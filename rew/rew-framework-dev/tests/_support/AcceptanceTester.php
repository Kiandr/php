<?php

namespace REW\Test;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    /**
     * relative url to access backend dashboard/control panel
     */
    const BACKEND_URL = '/backend/';

    /**
     * arbitrary name to store/retrieve session data
     */
    const SESSION_NAME = 'login';
    private static $AUTHENTICATED;
    private static $currentUser;

    /**
     * css selectors
     */
    const LOGIN_FORM            = ['css' => 'body > div:nth-child(1) > div > form'];
    const SUBMIT_BUTTON         = ['css' => '#app__main > div > form > div.btns > button'];
    const CRM_NOTIFICATION_DIV  = ['css' => '.ui-notify-message'];      //could be .notify-message in some

    /**
     * login to the backend dashboard.
     * (basic auth credentials are prepended to the URL parameter in the yml config file.)
     */
    public function login($user, $password = '')
    {
        if ($user != static::$currentUser) {
            $this->reset();
            static::$currentUser = $user;
        }

        if (!static::$AUTHENTICATED) {
            $this->amOnPage(static::BACKEND_URL);
            $this->waitForElementVisible(static::LOGIN_FORM, 5);
            $this->submitForm(static::LOGIN_FORM, [
                'username' => $user,
                'password' => $password
            ], static::SUBMIT_BUTTON);
            $this->saveSessionSnapshot(static::SESSION_NAME);
            static::$AUTHENTICATED = true;
            // reconfigure the base domain address to remove the basic auth credentials
            $this->reconfigureDomain();
        } else {
            $this->loadSessionSnapshot(static::SESSION_NAME);
        }
    }

    /**
     * Reset the User cookies
     */
    public function reset()
    {
        $this->resetCookie('PHPSESSID');
        $this->resetCookie('rew-auth');
        static::$AUTHENTICATED = false;
    }

    /**
     * @param null $password
     * Log in as the admin without need for password to simplify for people who dont know where the properties are
     */
    public function loginAsAdmin($password = null)
    {
        if (is_null($password)) {
            $password = $this->grabFromConfig('default_admin_password');
        }
        $this->login('admin', $password);
    }
    public function navTo($page)
    {
        // reconfigure the base domain address to remove the basic auth credentials
        $this->reconfigureDomain();
        $this->amOnPage($page);
    }

    /**
     * reconfigure the base domain address to remove the basic auth credentials
     */
    public function reconfigureDomain()
    {
        $domain = $this->grabFromConfig('domain');
        $prefix = $this->grabFromConfig('domainPrefix');
        $scheme = parse_url($prefix . $domain, PHP_URL_SCHEME);
        $scenario = $this->getScenario();
        $webDriver = $scenario->current('modules')['WebDriver'];
        $webDriver->_reconfigure(['url' => $scheme . '://' . $domain]);
    }
}
