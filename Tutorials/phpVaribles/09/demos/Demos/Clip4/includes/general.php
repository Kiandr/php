<?php

session_start();

if(isset($_SESSION['firstName']) ) 
{
	$firstName = $_SESSION['firstName'];
}