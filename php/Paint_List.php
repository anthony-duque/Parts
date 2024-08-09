<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

// echo $method;

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      var_dump($data);
      ProcessPOST($data);
      break;

   case "PUT":    // Could read from input and query string
   //         $qString = $_GET["id"];
   //         echo "PUT = " . $qString;
      echo 'PUT';
      $json = file_get_contents('php://input');
      $data = json_decode($json);
//      var_dump($data);
      break;

   case "GET":
//      $carList = ProcessGET();
//``      echo json_encode($carList);
      break;

   case "DELETE":
      echo 'DELETE';
      $qString = $_GET["id"];
      echo "DELETE = " . $qString;
      break;

   default:
      break;
}


function ProcessPOST($listOfCars){

    require('db_open.php');

    $tsql = "DELETE FROM Work_Queue WHERE Dept_Code = 'P'";

	if ($conn->query($tsql) === TRUE) {
		echo "<br/>Repairs Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br> - " . $conn->error;
	  exit;
	}

    $tsql = "INSERT INTO Work_Queue " .
             "(RO_Num, Priority, Dept_Code, Status) ";

    foreach($listOfCars as $eachCar){

        $values = "VALUES (". $eachCar->RONum . ", " . $eachCar->Priority . ", '" .
                $eachCar->DeptCode . "', '" .$eachCar->Status . "')";

        if ($conn->query($tsql . $values) === TRUE) {
	      ;// echo $ro_num . " uploaded<br/>";
	    } else {
	      echo "Error: " . $tsql . $values . "<br>" . $conn->error;
	    }
    }

    $conn = null;

}   //   ProcessPOST()

?>
