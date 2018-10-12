<?php

use REW\Test\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('visit site and see REW backlink');
$I->amOnPage('/');
$I->seeResponseCodeIs(200);
$I->seeElement('a', ['href' => 'http://www.realestatewebmasters.com/']);
