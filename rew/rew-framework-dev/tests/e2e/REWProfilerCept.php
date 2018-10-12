<?php
/**
 * @author "Brennon Obset <obst.brennon@realestatewebmasters.com>"
 */

use REW\Test\AcceptanceTester;

$I = new AcceptanceTester($scenario);

$I->wantTo('ensure REW profiler is disabled');
$I->amOnPage('/');
$I->seeInTitle('Your Real Estate Website');
$I->dontSeeElement('#profile-report-controls');
