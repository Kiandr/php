<?php

session_start();

if(isset($_SESSION['firstName']) ) 
{
	$firstName = $_SESSION['firstName'];
}

if(isset($_SESSION['lastName']) ) 
{
	$lastName = $_SESSION['lastName'];
} 
else 
{
	$_SESSION['lastName'] = "Rocks;
	$lastName = $_SESSION['lastName'];
}