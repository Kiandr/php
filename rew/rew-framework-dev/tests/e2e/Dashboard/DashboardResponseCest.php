<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\DashboardPage;
use Faker;

class DashboardResponseCest
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
        $I->wantTo('verify daphine demos / agent2 can respond to an assigned lead');
        $I->login('agent2', '3UjG8egS');
        $util->verifyTextPresent(DashboardPage::$pageTitle);
        $I->fillField(DashboardPage::$nancyFultonResponseField, $faker->paragraph);
        $I->click(DashboardPage::$nancyFultontResponseSendBtn);
        $util->checkNotificationNoJS('Action Successful! A message has been sent to Nancy Fulton.');
        $util->checkForExceptions();
    }
}
