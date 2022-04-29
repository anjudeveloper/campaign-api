<?php  
    class dbConnect {  
        function __construct() {  
            require_once('config.php');  
            $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABSE);  
          
            if(!$conn)// testing the connection  
            {  
                die ("Cannot connect to the database");  
            }   
            return $conn;  
        }  
        
    }  
?> 