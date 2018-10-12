<?php

class Db{
public static $userName = "admin";
public static $passWord ="Administrator";
public static $server ="localhost";
public static $dbName ="Test";
public static $connection;
function __construct(){
    $dbPassword = "administrator";
    $dbUserName = "admin";
    $dbServer = "localhost";
    $dbName = "Test";
    $connection = new mysqli($dbServer, $dbUserName, $dbPassword, $dbName);
    echo "Data base";
    //$this::$connection = new mysql($this->server, $this->username,$this->passWord, $this->dbName);
    echo $connection;
    
    if($connection->connect_errno)
{
    //echo "Database Connection Failed. Reason: ".$connection->connect_error;
    exit("Database Connection Failed. Reason: ".$connection->connect_error);
}
}
function connect(){}
function insertId(){}
function PreparedStatement(){}
function Select(){}
}
?>