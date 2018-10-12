<?php

namespace REW\Test\CRM;

use Faker;
use REW\Test\Page\Backend\Util;
use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateGroupCest
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
        $faker = Faker\Factory::create();
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new group');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewGroupURL);
        $I->seeInPageSource(CRMPage::$addNewGroupTitle);
        $myCRMPage->addGroup($faker->company);
        $util->checkNotification('Action Successful! Group has successfully been created.');
        $util->checkForExceptions();
    }
}
