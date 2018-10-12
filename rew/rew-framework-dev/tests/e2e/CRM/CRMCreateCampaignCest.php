<?php
namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use Faker;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;

class CRMCreateCampaignCest
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
        $myCRMPage = new CRMPage($I);
        $util = new Util($I);
        $faker = Faker\Factory::create();
        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new campaign');
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$addNewCampaignURL);
        $I->seeInPageSource(CRMPage::$addNewCampaignTitle);
        $myCRMPage->addCampaign($faker->company, $faker->jobTitle);
        $util->checkNotification('Action Successful! Campaign has successfully been created.');
        $util->checkForExceptions();
    }
}
