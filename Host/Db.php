<?php

class Db{

private $dbPassword = "root";
private $dbUserName = "root";
private $dbServer = "localhost";
private $dbName = "Test";
function __construct(){}

public function InsertIntoUser($name, $gender, $color){
    $connection = new mysqli($this->dbServer, $this->dbUserName, $this->dbPassword, $this->dbName);
    echo "<pre>";
    print_r($user);
    print_r($connection);

    $query = "INSERT INTO users (name, gender, colour) VALUES ('$name', '$gender', '$color')";
    print_r($query);
    $connection->query($query);
    
    if($connection->connect_errno)
    {
        echo "Database Connection Failed. Reason: ".$connection->connect_error;
        exit("Database Connection Failed. Reason: ".$this->myConnection->connect_error);
    }
    $connection->close();
}

public function GetAlLFromUser(){


    $connection = new mysqli($this->dbServer, $this->dbUserName, $this->dbPassword, $this->dbName);
    echo "<pre>";
    print_r($user);
    print_r($connection);

    $query = "select  * from users";
    echo $query;
    $result = mysqli_query($connection, $query);
    foreach ($result as $row){
        echo "<li>";
        echo htmlspecialchars($row['name']);
        echo htmlspecialchars($row['gender']);
        echo htmlspecialchars($row['colour']);
        echo "</li>" ;
    }


    
    if($connection->connect_errno)
    {
        echo "Database Connection Failed. Reason: ".$connection->connect_error;
        exit("Database Connection Failed. Reason: ".$this->myConnection->connect_error);
    }
    $connection->close();
    return result;
}

public function connect(){
$dbPassword = "root";
$dbUserName = "root";
$dbServer = "localhost";
$dbName = "Test";

$connection = new mysqli($dbServer, $dbUserName, $dbPassword, $dbName);
echo"<pre>";
print_r($connection);
$todayIs =   date("l");
$query = "INSERT INTO Author (Name, LastName, Title, year) VALUES ('$todayIs', 'RRR', 'BookLLL', 1986)";

print_r($query);

$connection->query($query);

if($connection->connect_errno)
{
    //echo "Database Connection Failed. Reason: ".$connection->connect_error;
    exit("Database Connection Failed. Reason: ".$this->myConnection->connect_error);
}

$this->myConnection = $connection;
    

}
function insertId(){
    if($this->myConnection->connect_errno)
    {
        exit("Database Connection Failed. Reason: ".$connection->connect_error);
    }
    
    //-- Change Values to create a new author
    //$query = "INSERT INTO Authors ('Name', 'LastName', 'Title', 'Year') VALUES ('John Ronald Reuel', 'Tolkien', 'Tolkien',200)";
    //INSERT INTO `Author` (`Name`, `LastName`, `Title`, `year`) VALUES ('Omid', 'Rad', 'Book', '3982');
    $var = date("l");
    //$query = "SELECT first_name, last_name, pen_name FROM Authors WHERE first_name = ?";
    $quert = "INSERT INTO Author (Name, LastName, Title, year) VALUES ('$var', 'Kian', 'Book', '3982')";
//    $statementObj = $this->myConnection->prepare($query);

  //  $statementObj->bind_param("s", "kianKian");
   // $statementObj->execute();

    //$statementObj->bind_result($firstName, $lastName, $penName);
    //$statementObj->store_result();

    $this->connect();
    $this->myConnection->query($query);
    
    echo "Newly Created Author Id:". $connection->insert_id;
    
    $this->myConnection->close();

}
function PreparedStatement(){}
function Select(){
    if($this->myConnection->connect_errno)
    {
        exit("Database Connection Failed. Reason: ".$this->myConnection->connect_error);
    }
    
    //$query = "UPDATE Authors SET pen_name = 'L. M. Montgomery' WHERE id = 2";
    //$query = "DELETE FROM Authors WHERE id = 4";
    //$query = "INSERT INTO Authors (first_name, last_name, pen_name) VALUES ('Arthur Ignatius Conan', 'Doyle', 'Sir Arthur Ignatius Conan Doyle')";
    $query = "SELECT * FROM Author ";
    $resultObj = $this->myConnection->query($query);
print_r($resultObj);
    if ($resultObj->num_rows > 0) 
    {
        while ($singleRowFromQuery = $resultObj->fetch_array()) 
        {
            print_r($singleRowFromQuery);
            echo "Author: ".$singleRowFromQuery['Name'].PHP_EOL;
        }
    }   
    $this->myCconnection->close();
    

}
function Close(){
    $this->myConnection->close();
}
}

?>