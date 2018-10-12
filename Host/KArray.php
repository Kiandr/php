<?php
class KArray{
    
        protected $AuthorArray ; 
        protected $AuthorAssositiveArray ; 
    
    function __construct(){
        echo "KArray was constructed!\n";
        $this->AuthorArray = array();
        $this->AuthorAssositiveArray=array();;
       // $this->setAuthorAssositiveArray();
    }
    // public function MultiArrayPrint(){}      
     public function setAuthorAssositiveArray($model){    
         foreach ( $model as $key => $value){
            //echo "Key".$key."Value".$value.'\n';
            array_push ($this->AuthorAssositiveArray,$key,$value); 
        }
    }

     public function printAuthorArray(){
        echo "<br>Printing</br>";
        foreach ($this->AuthorAssositiveArray as $key => $value ){
            echo $key."->".$value."</br>";
        }
        
        print count($this->AuthorAssositiveArray, COUNT_RECURSIVE);
    }   

}

?>