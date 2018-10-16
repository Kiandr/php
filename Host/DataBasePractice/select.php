<?php 
require_once "DbUsers.php";

//print_r (new DbUsers());
$temp = new DbUsers();
$connection = $temp->myConnection();
?>