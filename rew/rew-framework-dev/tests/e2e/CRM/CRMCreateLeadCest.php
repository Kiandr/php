<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use Faker;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateLeadCest
{
    public function _before(AcceptanceTester $I)
    {
        //pop the lead create notification
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group crm
     */
    public function createLead(AcceptanceTester $I)
    {
        $myCRMPage = new CRMPage($I);
        $util = new Util($I);
        $faker = Faker\Factory::create();
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new lead');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewLeadURL);
        $I->seeInPageSource(CRMPage::$addNewLeadTitle);
        $myCRMPage->addLead($faker->email, $faker->firstName, $faker->lastName);
        $util->checkNotificationNoJS('Action Successful! Lead has successfully been created.');
        $util->checkForExceptions();
    }
}
