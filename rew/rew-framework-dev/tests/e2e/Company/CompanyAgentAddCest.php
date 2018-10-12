<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\CompanyPage;
use REW\Test\Page\Backend\Util;

class CompanyAgentAddCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group company
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $company = new CompanyPage($I);
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add new agent');
        $I->loginAsAdmin();
        $I->navTo(CompanyPage::$addAgentURL);
        $util->verifyTextPresent(CompanyPage::$addAgentTitle);
        $company->addNewAgent();
        $util->checkNotification('Action Successful! Agent has successfully been created.');
        $util->checkForExceptions();
    }
}
