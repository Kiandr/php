<?php use REW\Test\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('make sure REW API docs requires authentication');
$I->sendGET('/api/docs/');
$I->seeResponseCodeIs(401);
$I->seeResponseEquals('You must authenticate before you can view this page. The username is \'crm\' and the password is your Application\'s API Key.');