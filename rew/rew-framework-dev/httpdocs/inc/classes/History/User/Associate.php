<?php

/**
 * History_User_Associate
 */
class History_User_Associate extends History_User
{

    /**
     * User Type
     * @var string
     */
    protected $type = History_User::TYPE_ASSOCIATE;

    /**
     * Load Associate Row from Database
     *
     * @return null|array
     * @throws PDOException
     */
    function getUserRow()
    {
        try {
            // Set User Data
            $this->data = $this->db->fetch("SELECT * FROM `associates` WHERE `id` = '" . $this->user . "';");

            // Return User Data
            return $this->data;

        // Query Error
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Display Link to Associate Summary
     *
     * @return HTML Anchor Link
     */
    public function displayLink()
    {
        if ($this->getUserData('id') < 1) {
            return '(Unknown Associate)';
        }
        return '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'associates/associate/summary/?id=' . $this->getUserData('id') . '">' . $this->getUserData('first_name') . ' ' . $this->getUserData('last_name') . '</a>';
    }
}
