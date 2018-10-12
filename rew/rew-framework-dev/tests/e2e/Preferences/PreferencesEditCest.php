<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\AdminPreferencesPage;

class PreferencesEditCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group preferences
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $preferences = new AdminPreferencesPage($I);
        /*
         *  Test Cases
         */
        $I->wantTo('verify charlie angus / agent1 / admin can edit his own preferences');
        //verify the expected leads for agent1 and accept 1
        $I->login("agent1", '3UjG8egS');
        $I->navTo($preferences::$URL);
        $I->click($preferences::$hamburger);
        $util->waitForAsyncJS();
        $I->click(['link'=>'Preferences']);
        $util->verifyTextPresent('Preferences (Edit)');
        $util->checkForExceptions();
        $preferences->editUser();
        $util->checkNotification('Action Successful! Your changes have successfully been saved.');
    }
}
