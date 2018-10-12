<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\ContentPage;
use REW\Test\Page\Backend\Util;

class ContentAddPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group content
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $content = new ContentPage($I);

        /*
         *  Test Cases
         */
        $I->wantTo('add new page');
        $I->loginAsAdmin();
        $I->navTo(ContentPage::$URL);
        $util->verifyTextPresent(ContentPage::$pageTitle);
        $content->addNewPage();
        $util->checkNotification('Action Successful! CMS Page has successfully been created.');
        $util->checkForExceptions();
    }
}
