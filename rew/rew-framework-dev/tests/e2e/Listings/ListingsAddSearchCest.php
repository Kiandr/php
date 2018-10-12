<?php

namespace REW\Test\Listings\Searches;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Listings\SearchesPage;
use REW\Test\Page\Backend\Util;

class ListingsAddSearchCest
{
    /**
     * tests adding an IDX Custom Search
     * @param \REW\Test\AcceptanceTester $I
     * @group listings
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);
        $searches = new SearchesPage($I);

        // Test cases
        $I->wantTo('verify an admin can add an IDX Custom Search');
        $I->loginAsAdmin();
        $I->navTo(SearchesPage::$URL);
        $I->click(SearchesPage::$addNewButton);
        $I->seeInTitle('Add IDX Search');
        $I->waitForElementNotVisible('#notifications', 10);
        $searches->addNewSearch();
        $util->checkNotification('Action Successful! Custom IDX Search has successfully been created.');
        $util->checkForExceptions();
    }
}
