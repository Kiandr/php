<?php

/**
 * History_IEditable
 * @package History
 */
interface History_IEditable extends History_IExpandable
{

    /**
     * Get Editable Data
     * @return array Editable Data
     */
    public function getEditable();

    /**
     * Get Edit Form
     * @return string HTML Form
     */
    public function getEditForm();
}
