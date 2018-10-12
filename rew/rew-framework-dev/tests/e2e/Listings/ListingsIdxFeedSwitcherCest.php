<?php

namespace REW\Test\Listings\Searches;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;

class ListingsIdxFeedSwitcherCest
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
     * Toggle currently selected IDX feed
     * @param \REW\Test\AcceptanceTester $I
     * @group listings
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $util = new Util($I);

        // Toggle currently selected IDX feed
        $I->wantTo('verify the IDX switcher toggles current IDX feed');
        $I->loginAsAdmin();
        $I->navTo(self::DEFAULT_SEARCH_URL);

        // Expect to see hidden IDX feed value in field
        $I->seeInField(self::HIDDEN_IDX_FIELD, 'mfr');

        // Toggle currently selected IDX feed
        $I->see('Florida', '#feedSwitcher');
        $I->click('#feedSwitcher');
        $I->click('Mid-South', '.menu__list--feeds');

        // Expect to see hidden IDX feed value in field
        $I->seeInField(self::HIDDEN_IDX_FIELD, 'realtracs');

        // Submit form and ensure selected IDX is chosen
        $I->submitForm('form#idx-builder-form', []);
        $util->checkNotification(self::SUCCESS_NOTIFICATION);
        $I->seeInField(self::HIDDEN_IDX_FIELD, 'realtracs');
    }
}
