<?php use REW\Test\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('visit a non-existant page');
$I->amOnPage('/doesnotexist');
$I->see('404');
