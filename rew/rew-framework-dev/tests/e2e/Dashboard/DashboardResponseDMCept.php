<?php
/**
 * @group dashboard
 */
namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\DashboardPage;
use REW\Test\Page\Backend\Util;
use Faker;

$I = new AcceptanceTester($scenario);
$dashboard = new DashboardPage($I);
$util = new Util($I);
$faker = Faker\Factory::create();

$I->wantTo('Verify admin can respond to a direct message from Lloyd Johnson');

//When I log in as admin
$I->loginAsAdmin();

//Given I am on the dashboard page
$I->amOnPage(DashboardPage::$URL);

//When I respond to a direct message
$dashboard->respondToDirectMessage($faker->paragraph());

//Then I see the notifications for 3 seconds
$util->checkNotificationNoJS('Action Successful! A message has been sent to Lloyd Johnson');

//When I press refresh
$I->reloadPage();

////Then there is no notification or repeat submition
$I->dontSeeElement(Util::$crmNotificationDiv);
