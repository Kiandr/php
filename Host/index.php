<?php

//include_once 'Person.php';
//include_once 'Author.php';
//include_once 'KArray.php';
//require_once 'Form.php';
include_once 'Db.php';

$mydb = new Db();
$mydbdb->GetAlLFromUser();

//$newAuthor = new Author("Samuel Langhorne", "Clemens", 1899);

//$newAuthor = new Author ("test",10,"rad");
// $authorsAssociative = array( 
//     "quarky" => "Charles Dickens", 
//     "brilliant" => "Jane Austin", 
//     "poetic" => "William Shakespeare"
// );

// $karray = new KArray();
// $karray->setAuthorAssositiveArray($authorsAssociative);
// $karray->printAuthorArray();

// $myDb = new Db();
// $myDb->connect();
// $myDb->Select();

// Building Forms
