<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use Faker;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateFormLetterCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group crm
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $crmPage = new CRMPage($I);
        $util = new Util($I);
        $faker = Faker\Factory::create();
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new form letter');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewFormLettersURL);
        $I->seeInPageSource(CRMPage::$addNewFormLettersTitle);
        $I->click(CRMPage::$addNewFormLettersAddButton);
        $crmPage->addFormLetters($faker->name);
        $util->checkNotification('Action Successful! Your new document category has successfully been created.');
        $util->checkForExceptions();
    }
}
