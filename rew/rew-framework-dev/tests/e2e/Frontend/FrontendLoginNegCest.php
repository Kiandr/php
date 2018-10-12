<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Frontend\HomePage;

class FrontendLoginNegCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \REW\Test\AcceptanceTester $I
     * @group frontend
     */
    public function tryToTest(AcceptanceTester $I)
    {
        $homePage = new HomePage($I);
        $util = new Util($I);

        //Log in with invalid user
        $I->wantTo('verify an unknown user cannot log in to the frontend');
        $I->amOnPage(HomePage::$URL);
        $homePage->login(HomePage::$unknownUserEmail);
        $util->waitFor(HomePage::$loginDialogErrorDiv);
        $util->verifyTextPresent(HomePage::$loginError);
    }
}
