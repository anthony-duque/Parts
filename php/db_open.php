<?php

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   function exceptionHandler($exception){
     echo "<h1>Failure</h1>";
     echo "Uncaught exception: ", $exception->getMessage();
     // echo "<h1>PHP info for troubleshooting.</h1>"
   }

   function formatErrors($errors)
   {
       // Display errors
       echo "<h1>SQL Error:</h1>";
       echo "Error information: <br/>";
       foreach ($errors as $error) {
           echo "SQLSTATE: ". $error['SQLSTATE'] . "<br/>";
           echo "Code: ". $error['code'] . "<br/>";
           echo "Message: ". $error['message'] . "<br/>";
       }
   }


   set_exception_handler('exceptionHandler');

   // Establishes the connection.
   $connect_string = "mysql:server=localhost;port=3306;dbname=CarStar";

   try{
       $conn = new PDO($connect_string, 'root', 'Al@d5150');
   }catch(PDOException $e){
       $conn = null;
       echo "DB Connection could not be established.";
   }
   // $conn = sqlsrv_connect($serverName, $connectOptions);
   // if ($conn == false){
   //   die(formatErrors(sqlsrv_errors()));
   // }

?>
