<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CompanyPage;

class CompanyAgentWebsiteCest
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
        //log in as admin, go to edit an agent
        //set up website
        //log into website and verify agent name

        $util = new Util($I);
        $company = new CompanyPage($I);
        $domain = $I->grabFromConfig('domain');
        $subdomain = CompanyPage::$agentLink;
        $prefix = $I->grabFromConfig('domainPrefix');
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can set up agent website domains');
        $I->loginAsAdmin();
        $I->navTo(CompanyPage::$URL);
        $util->verifyTextPresent(CompanyPage::$pageTitle);
        $company->enableAgentWebsite();
        $util->checkNotification('Action Successful! Your changes have successfully been saved.');
        $util->checkForExceptions();
        $I->amOnUrl($prefix . $subdomain . '.' . $domain);
        $I->amOnPage('/');
        $I->see(CompanyPage::$agent1Name);
    }
}
