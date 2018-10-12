<?php

use REW\Core\Interfaces\Backend\ContactSnippetsInterface;

require_once __DIR__ . '/../../../backend/inc/php/functions/funcs.ContactSnippets.php';

/**
 * First part of refactoring the procedural backend snippets. The code should be moved here and the old functions
 * deprecated.
 * Class Backend_ContactSnippets
 */
class Backend_ContactSnippets implements ContactSnippetsInterface
{
    /**
     * Get Submission Message from POST
     *
     * @return string HTML Message
     */
    function getFormVars()
    {

        return getFormVars();
    }

    /**
     * Collect data from contact forms and add the user to the database
     *
     * $fieldList is an array of all the values known about the user
     *
     * array (
     *        'first_name'      => 'Bob',
     *        'last_name'       => 'Smith',
     *        'password'        => 'pass',
     *        'email'           => 'test@test.com',
     *        'address1'        => '123 test street',
     *        'address2'        => '#3',
     *        'address3'        => 'Atten: Owner',
     *        'city'            => 'Nanaimo',
     *        'country'         => 'Canada',
     *        'state'           => 'BC',
     *        'zip'             => '12345',
     *        'phone'           => '123-4567',
     *        'comments'        => 'user submitted comments',
     *        'subject'         => 'subject for email msg to agent',
     *        'message'         => 'email msg to agent',
     *        'phone_work'      => '123-4567',
     *        'phone_fax'       => '123-4567',
     *        'agent'           => '3',
     *        'forms'           => 'Contact Form',
     *        'opt_marketing'   => 'in',
     *        'opt_searches'    => 'out',
     *        'opt_texts'       => 'out'
     * )
     *
     * $ar_id is the Auto Responder ID to user, 0 to disable
     *
     * @param array $fieldList
     * @param int $autoResponderID Auto-Responder ID
     * @return int Lead ID
     */
    public function collectContactData($fieldList, $autoResponderID)
    {

        return collectContactData($fieldList, $autoResponderID);
    }

    /**
     * Collect Data from Form
     *
     * @param integer $autoResponderID
     * @param string $form
     * @param boolean $testAddress
     * @param boolean $testPhone
     * @param boolean $testName
     * @return string Response Message
     */
    public function contactForm($autoResponderID, $form, $testAddress = false, $testPhone = false, $testName = true)
    {

        return contactForm($autoResponderID, $form, $testAddress, $testPhone, $testName);
    }
}
