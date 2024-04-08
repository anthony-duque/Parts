<?php

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   $servername = "localhost";
   $username = "root";
   $password = "Al@d5150";
   $dbname = "CarStar";

        // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
   if ($conn->connect_error){
     die("Connection failed: " . $conn->connect_error);
//    } else {
//       echo("Connection successful!");
   }

//   $conn = null;
// exit;

?>
