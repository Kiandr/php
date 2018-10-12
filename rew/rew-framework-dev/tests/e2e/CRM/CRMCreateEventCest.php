<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateEventCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group crm
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $myCRMPage = new CRMPage($I);
        $util = new Util($I);
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new event');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewEventURL);
        $I->seeInPageSource(CRMPage::$addNewEventTitle);
        $myCRMPage->addEvent(CRMPage::$addNewEventName);
        $util->checkNotification('Action Successful! Your calendar event has successfully been saved.');
        $util->verifyTextPresent(CRMPage::$addNewEventName, 5);
        $util->checkForExceptions();
    }
}
