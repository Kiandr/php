<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CompanyPage;

class CompanyAgentEditCest
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
        $I->navTo(CompanyPage::$agentsURL);
        $util->verifyTextPresent(CompanyPage::$pageTitle);
        $util->waitForAsyncJS();
        $util->checkForExceptions();
        $company->editAgent();
        $util->checkNotification('Action Successful! Your changes have successfully been saved.');
        $util->checkForExceptions();
    }
}
