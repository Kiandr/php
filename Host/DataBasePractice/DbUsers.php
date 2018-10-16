<?php 
class DbUsers{

    private $dbPassword = "root";
    private $dbUserName = "root";
    private $dbServer = "localhost";
    private $dbName = "Test";
    private $gConnection; 

    public function __construct(){}
    public function myConnection(){
        
        return  new mysqli($this->dbServer, $this->dbUserName, $this->dbPassword, $this->dbName);
        
    }    
}
?>