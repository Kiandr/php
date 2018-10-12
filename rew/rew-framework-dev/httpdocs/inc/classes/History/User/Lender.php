<?php

/**
 * History_User_Lender
 */
class History_User_Lender extends History_User
{

    /**
     * User Type
     * @var string
     */
    protected $type = History_User::TYPE_LENDER;

    /**
     * Load Lender Row from Database
     *
     * @return null|array
     * @throws PDOException
     */
    function getUserRow()
    {
        try {
            // Set User Data
            $this->data = $this->db->fetch("SELECT * FROM `lenders` WHERE `id` = '" . $this->user . "';");

            // Return User Data
            return $this->data;

        // Query Error
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Display Link to Lender Summary
     *
     * @return HTML Anchor Link
     */
    public function displayLink()
    {
        if ($this->getUserData('id') < 1) {
            return '(Unknown Lender)';
        }
        return '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'lenders/lender/summary/?id=' . $this->getUserData('id') . '">' . $this->getUserData('first_name') . ' ' . $this->getUserData('last_name') . '</a>';
    }
}
