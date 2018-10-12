<?php

namespace REW\Test\CRM;

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\DashboardPage;
use REW\Test\Page\Backend\Util;
use REW\Test\Page\Backend\CRMPage;
use REW\Test\Page\Backend\CompanyPage;

class CRMLeadPermissionsCest
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
        $util = new Util($I);

        $teamUrl = CompanyPage::$teamsURL;
        $leadUrl = CRMPage::$URL;
        $teamName = CompanyPage::$teamName;
        $buttonElement = CompanyPage::$companySubmitButton;

        $ownerAgentName = 'George Harper';
        $viewerAgentName = 'Daphne Demos';
        $leadName = 'Johnny Adams';
        $secondLeadName = 'Carol Wright';

        $I->loginAsAdmin();

        $I->wantTo('verify different agents lead permissions');

        // Set owning agent to george harper / agent2 in permissions
        $I->navTo($teamUrl);
        $I->click($teamName);
        $util->verifyTextPresent($ownerAgentName);
        $I->click(CompanyPage::$georgeTeamEditLink);
        $I->click(CompanyPage::$accessLeadsFalseBox);
        $I->click(CompanyPage::$shareLeadsFullBox);
        $I->click($buttonElement);
        $util->verifyTextPresent('Action Successful! This team member\'s permissions have successfully been updated.');

        // Set viewing agent (Daphne Demos) permissions
        $I->navTo($teamUrl);
        $I->click($teamName);
        $util->verifyTextPresent($viewerAgentName);
        $I->click(CompanyPage::$daphneTeamEditLink);
        $I->click(CompanyPage::$accessLeadsFullBox);
        $I->click(CompanyPage::$shareLeadsFalseBox);
        $I->click(CompanyPage::$companySubmitButton);
        $util->verifyTextPresent('Action Successful! This team member\'s permissions have successfully been updated.');

        // Assign the lead to the owning agent (George Harper)
        $I->navTo($leadUrl);
        $util->verifyTextPresent($leadName);
        $I->click($leadName);
        $util->verifyTextPresent($summaryText = 'Lead Summary');
        $I->click($summaryText);
        $util->verifyTextPresent($editText = 'Edit Mode');
        $I->click($editText);
        $I->selectOption(CompanyPage::$assignAgentBox, $ownerAgentName);
        $I->selectOption(CompanyPage::$leadStatusBox, 'Accepted');
        $I->click($buttonElement);
        $util->verifyTextPresent('Action Successful! ' . $leadName . ' has been assigned to Agent: ' . $ownerAgentName . 'Your changes have successfully been saved.');

        $I->navTo($leadUrl);
        $util->verifyTextPresent($secondLeadName);
        $I->click($secondLeadName);
        $util->verifyTextPresent($summaryText = 'Lead Summary');
        $I->click($summaryText);
        $util->verifyTextPresent($editText = 'Edit Mode');
        $I->click($editText);
        $I->selectOption(CompanyPage::$assignAgentBox, $viewerAgentName);
        $I->selectOption(CompanyPage::$leadStatusBox, 'Accepted');
        $I->click($buttonElement);
        $util->verifyTextPresent('Action Successful! ' . $secondLeadName . ' has been assigned to Agent: ' . $viewerAgentName . 'Your changes have successfully been saved.');

        // agent2 == Daphne Demos (viewer) - test that they're in the list and can be viewed/edited
        $I->login('agent2', '3UjG8egS');
        $I->amOnPage($leadUrl);
        $I->canSee($leadName);
        $I->click($leadName);
        $I->canSee('Lead Summary');
        $util->verifyTextPresent($phn = '241-251-7264');
        $I->canSee($phn);
        $I->click($summaryText = 'Lead Summary');
        $util->verifyTextPresent($editText = 'Edit Mode');
        $I->click($editText);
        $I->canSee('Lead (Edit)');
        $I->seeInField(CompanyPage::$phoneField, $phn);

        $I->amOnPage($leadUrl);
        $I->canSee($secondLeadName);
        $I->click($secondLeadName);
        $I->canSee('Lead Summary');
        $util->verifyTextPresent($phn = '213-770-5163');
        $I->canSee($phn);
        $I->click($summaryText);
        $util->verifyTextPresent($editText = 'Edit Mode');
        $I->click($editText);
        $I->canSee('Lead (Edit)');
        $I->seeInField('[name=phone]', $phn);

        // agent3 == George Harper (owner) - test that lead is in the list and can be viewed/edited
        $I->login('agent3', '3UjG8egS');
        $I->amOnPage($leadUrl);
        $I->canSee($leadName);
        $I->cantSee($secondLeadName);
        $I->click($leadName);
        $util->verifyTextPresent($phn = '241-251-7264');
        $I->canSee('Lead Summary');
        $I->canSee($phn);
        $I->click($summaryText);
        $util->verifyTextPresent($editText = 'Edit Mode');
        $I->click($editText);
        $I->canSee('Lead (Edit)');
        $I->seeInField('[name=phone]', $phn);

        // verify admin no longer sees assigned leads
        $I->loginAsAdmin();
        $I->navTo(CRMPage::$URL);
        $I->seeInPageSource('Leads, Unassigned');
        //Verify accepted leads do not appear in CRM list
        $I->dontSee('Carol Wright');
        $I->dontSee('Johnny Adams');
        $I->navTo(DashboardPage::$URL);
        $I->see(DashboardPage::$pageTitle);
        //Verify accepted leads do not appear in Dashboard
        $I->dontSee('Carol Wright');
        $I->dontSee('Johnny Adams');
    }
}
