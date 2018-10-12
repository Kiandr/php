<?php
/**
 * @group frontend
 */
use REW\Test\AcceptanceTester;
use REW\Test\Page\Frontend\ContactPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('make sure the contact form works');
$I->amOnPage(ContactPage::$URL);
$I->fillField(ContactPage::$firstNameField, 'John');
$I->fillField(ContactPage::$lastNameField, 'Smith');
$I->fillField(ContactPage::$emailField, 'jsmith@realestatewebmasters.com');
$I->fillField(ContactPage::$commentsField, 'Hello world!');
$I->click(ContactPage::$submitButton);
$I->waitForText("We'll get back to you as soon as possible.");
