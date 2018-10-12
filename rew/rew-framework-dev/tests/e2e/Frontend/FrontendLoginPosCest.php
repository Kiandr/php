<?php

namespace REW\Test;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Frontend\HomePage;

class FrontendLoginPosCest
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

        //Log in with valid user
        $I->wantTo('verify a known user can log in to the frontend');
        $I->amOnPage(HomePage::$URL);
        $homePage->login(HomePage::$knownUserEmail);
        $I->seeInSource('Logout');
    }
}
