<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;

class DashboardLoginCest
{
    public function _before(AcceptanceTester $I, $scenario)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group dashboard
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('verify admin can log in to the backend');

        // Reset logged in
        $util = new Util($I);
        $util->reset();

        // Go to backend login
        $I->amOnPage('/backend/');
        $I->see('Sign In', 'h2');

        // No JS errors occurred on login form
        $errors = $I->executeJS('return window.errors;');
        $I->assertTrue(is_array($errors));
        $I->assertEmpty($errors);

        // Login to admin account
        $I->loginAsAdmin();
        $I->see('Dashboard', 'a.bar__title');

        // No JS errors occurred
        $errors = $I->executeJS('return window.errors;');
        $I->assertTrue(is_array($errors));
        $I->assertEmpty($errors);
    }
}
