<?php
require_once 'Person.php';

class Author extends Person
{
    public $penName = "Mark Twain";
    public static $centryPopular = "19th";
    public function getPenName()
    {
        return $this->penName.PHP_EOL;
    }    
    
    //-- Modified to include what the Author wrote
    public function getFullName()    
	{
        echo "Author->getFullName()".PHP_EOL;
        return $this->lastName." ".$this->FirstName.PHP_EOL;
    }
    public static function getCentryAuthorWasPopular(){
    return self::$centryPopular;
    }
}