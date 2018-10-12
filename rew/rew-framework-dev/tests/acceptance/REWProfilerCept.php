<?php use REW\Test\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure REW profiler is disabled');
$I->amOnPage('/');
$I->dontSeeElement('#profile-report-controls');
