<?php
/**
 * @group lenders
 */
use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\CRMPage;
use REW\Test\Page\Backend\Util;

$I = new AcceptanceTester($scenario);
$util = new Util($I);

$I->wantTo('verify when a Allison Jacobs / lender1 logs in she is directed to leads page');
$I->login('lender1', '2uWuxvUP');
$I->amOnPage(CRMPage::$URL);
