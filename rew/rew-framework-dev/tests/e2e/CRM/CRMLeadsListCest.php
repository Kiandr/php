<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;

class CRMLeadsListCest
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
        $util = new Util($I);
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can see all crm leads');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$URL);
        $I->seeInPageSource('Leads, Unassigned');
        //Verify list of Leads
        $util->verifyTextPresent('Lloyd Johnson');
        $util->verifyTextPresent('Jim White');
        $util->verifyTextPresent('Jane Brown');
        $util->verifyTextPresent('Peter Hastings');
        $util->checkForExceptions();
    }
}
