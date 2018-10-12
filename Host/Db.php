<?php

class Db{
public static    $user = 'root';
public static    $password = 'root';
public static    $db = 'Test';
public static    $host = 'localhost';
public static    $port = 8889;
public static    $link;
public static    $connection;

function __construct(){
    //echo "DataBase connection";

    
    // $this->link = mysqli_init();
    // $success = mysqli_real_connect(
    //    $this->link,
    //    $this->host,
    //    $this->user,
    //    $this->password,
    //    $this->db,
    //    $this->port
    // );
    //echo $success;  
    $conn = mysql_connect('localhost', 'root', 'root');
   // echo $conn;



}

function connect(){
}
function insertId(){}
function PreparedStatement(){}
function Select(){}
}

?>