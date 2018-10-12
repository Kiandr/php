<?php

namespace REW\Test\Listings\Searches;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;

class ListingsIdxSearchDefaultsCest
{

    /**
     * URL to access IDX search defaults
     * @const string
     */
    const DEFAULT_SEARCH_URL = '/backend/idx/default-search/';

    /**
     * Notice message on successful submission
     * @const string
     */
    const SUCCESS_NOTIFICATION = 'Action Successful! Search Defaults have successfully been updated.';

    /**
     * @const string
     */
    const HIDDEN_IDX_FIELD = 'form#idx-builder-form input[type=hidden][name="idx"]';

    /**
     * Navigate to IDX search defaults
     * @param \REW\Test\AcceptanceTester $I
     * @group listings
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);

        // Login to REW CRM
        $I->wantTo('verify an admin can navigate to IDX search defaults');
        $I->loginAsAdmin();

        $I->waitForJS('return document.readyState == "complete"', 60);

        // Go to Listings section (from app menu)
        $I->waitForElement("//a[contains(@class, 'drop-target')]", 10);
        $I->waitForElementNotVisible('#notifications', 10);

        $I->click('[data-drop="#menu--apps"]');
        $I->click('Listings', '#menu--apps');

        // Go to Searches page (from sidebar menu)
        $I->click('Searches', '#app__sidebar nav');
        $I->see('IDX Searches', '.bar__title');

        // Go to "Default Search" settings page
        $I->click('Default Search', '#app__main .nodes__list');
        $I->see('Search Defaults', '.bar__title');
        $I->seeInCurrentUrl(self::DEFAULT_SEARCH_URL);

        // Submit form and ensure success notice is shown
        $I->submitForm('form#idx-builder-form', []);
        $util->checkNotification(self::SUCCESS_NOTIFICATION);
    }
}
