<?php

/**
 * History_User_Generic
 */
class History_User_Generic extends History_User
{

    public function getUserRow()
    {
        return null;
    }

    public function displayLink()
    {
        return '(Unknown User)';
    }
}
