<?php

/**
 * History_User_Lead
 */
class History_User_Lead extends History_User
{

    /**
     * User Type
     * @var string
     */
    protected $type = History_User::TYPE_LEAD;

    /**
     * Load Lead Row from Database
     *
     * @return null|array
     * @throws PDOException
     */
    function getUserRow()
    {
        try {
            // Set User Data
            $this->data = $this->db->fetch("SELECT * FROM `" . Settings::getInstance()->TABLES['LM_LEADS'] . "` WHERE `id` = '" . $this->user . "';");

            // Return User Data
            return $this->data;

        // Query Error
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Display Link to Lead Summary
     *
     * @return HTML Anchor Link
     */
    public function displayLink()
    {
        if ($this->getUserData('id') < 1) {
            return '(Unknown Lead)';
        }
        $first_name = $this->getUserData('first_name');
        $last_name = $this->getUserData('last_name');
        $link = (!empty($first_name) || !empty($last_name)) ? $first_name . ' ' . $last_name : $this->getUserData('email');
        return '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $this->getUserData('id') . '">' . $link . '</a>';
    }
}
