<?php

header('Access-Allow-Control-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

$task = "";
switch($method){

   case 'POST':
      echo 'POST';
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      //ProcessPOST($data);
      var_dump($data);
      //echo $data->mrn;
      break;

   case "PUT":    // Could read from input and query string
   //         $qString = $_GET["id"];
   //         echo "PUT = " . $qString;
      echo 'PUT';
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      var_dump($data);
      break;

   case "GET":
//         echo 'GET';
//         $qString = $_GET["id"];
      $patientList = ProcessGET();
      echo json_encode($patientList);
      break;

   case "DELETE":
      echo 'DELETE';
      $qString = $_GET["id"];
      echo "DELETE = " . $qString;
      break;

   default:
      $task = "Task unknown";
      break;
}

?>
