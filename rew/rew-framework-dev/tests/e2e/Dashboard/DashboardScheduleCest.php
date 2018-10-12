<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\CRM\CRMLeadPermissionsCest;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\DashboardPage;
use Faker;

class DashboardScheduleCest
{
    public function _before(AcceptanceTester $I, $scenario)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group dashboard
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $dashboard = new DashboardPage($I);
        $faker = Faker\Factory::create();
        $title = $faker->company;

        $I->wantTo('verify daphine demos / agent2 can schedule a lead');
        $I->login('agent2', '3UjG8egS');
        $util->verifyTextPresent(DashboardPage::$pageTitle);
        $dashboard->scheduleLead($title);
        $util->checkNotification('Action Successful! Your reminder has successfully been added.');
        $util->waitForAsyncJS();
        $util->verifyTextPresent($title);
        $util->checkForExceptions();
    }
}
