<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use Faker;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateActionPlanCest
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
        $I->wantTo('verify an admin can add new action plans');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewActionPlanURL);

        $I->seeInPageSource(CRMPage::$addNewActionPlanTitle);
        $I->click(CRMPage::$addNewActionPlanAddButton);
        $myCRMPage->addActionPlan($faker->name);
        $util->checkNotification('Action Successful! Action plan has successfully been created.');
        $util->checkForExceptions();
    }
}
