<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const TARGET_DIR    = "../images/return_forms/";  // destination folder on the server

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':;
      ProcessPOST();
      break;

   case "PUT":    // Could read from input and query string
      echo 'PUT';
      ProcessPUT();
      break;

   case "GET":
      $uploadedReturns = scandir(TARGET_DIR, SCANDIR_SORT_DESCENDING);
      echo json_encode($uploadedReturns);
      break;

   case "DELETE":
      $qString = $_GET["id"];
      echo "DELETE = " . $qString;
      break;

   default:
      break;
}


function ProcessPOST(){

    try{

        $targetFile = TARGET_DIR . basename($_FILES["returnForm"]["name"]);
        $upload_OK = move_uploaded_file($_FILES["returnForm"]["tmp_name"], $targetFile);

        if ($upload_OK){
            echo "<br/> Return Form/s uploaded successfully!";
            header("Location: ../Return_Forms.html");
        }

    } catch(Exception $e){
        echo "There was an error uploading the " . basename($_FILES["returnForm"]["name"]);
        header("Location: ../Upload_Returns.html");
    }   // try-catch()

}   // ProcessPOST()

?>
