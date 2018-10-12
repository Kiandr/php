<?php use REW\Test\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('login and view REW API docs using API key');
$I->amHttpAuthenticated('crm', $I->grabFromConfig('key'));
$I->sendGET('/api/docs/');
$I->seeResponseCodeIs(200);
$I->seeResponseContains('API Developer Reference');