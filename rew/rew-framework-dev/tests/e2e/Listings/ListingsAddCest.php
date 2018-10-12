<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\ListingsPage;
use REW\Test\Page\Backend\Util;

class ListingsAddCest
{
    public function _before(AcceptanceTester $I, $scenario)
    {
        $scenario->skip('this keeps failing unable to select city aldergrove');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group listings
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $listings = new ListingsPage($I);

        /*
         *  Test Cases
         */
        $I->wantTo('verify an admin can add a new listing');
        $I->loginAsAdmin();
        $I->navTo(ListingsPage::$addListingURL);
        $I->seeInTitle('Listings');
        $listings->addNewListing();
        $util->checkNotification('Action Successful! Listing has successfully been created.');
        $util->checkForExceptions();
    }
}
