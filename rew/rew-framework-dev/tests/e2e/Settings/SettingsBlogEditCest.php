<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\SettingsPage;

class SettingsBlogEditCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group settings
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $settings = new SettingsPage($I);
        /*
         *  Test Cases
         */
        $I->wantTo('verify charlie angus / agent1 / admin can update the blog');
        $I->login('agent1', '3UjG8egS');
        $I->navTo($settings::$blogURL);
        $util->verifyTextPresent($settings::$title);
        $util->checkForExceptions();
        $settings->editBlog();
        $util->checkNotification('Action Successful! Blog Settings have successfully been saved.');
        $util->checkForExceptions();
    }
}
