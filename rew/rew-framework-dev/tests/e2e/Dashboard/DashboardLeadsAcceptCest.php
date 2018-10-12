<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\DashboardPage;

class DashboardLeadsAcceptCest
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
        $I->wantTo('verify daphine demos / agent2 admin can accept assigned leads');

        $util = new Util($I);
        $dashboard = new DashboardPage($I);
        $I->login('agent2', '3UjG8egS');
        $dashboard->acceptLead();
        $util->checkNotificationNoJS('Action Successful! Linda Hopkins has successfully been updated.');
        $util->checkForExceptions();
    }
}
