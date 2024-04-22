<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

//echo $method;

$task = "";
switch($method){

   case 'POST':
//      echo 'POST';
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
//      var_dump($data);
      //echo $data->mrn;
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


function ProcessPOST($listOfCars){

    require('db_open.php');

    $tsql = "DELETE FROM WorkPriorities";

	if ($conn->query($tsql) === TRUE) {
//		echo "<br/>Repairs Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br> - " . $conn->error;
	  exit;
	}

    $tsql = "INSERT INTO WorkPriorities " .
             "(RONum, Priority, Tech_Index, Car_Index, WorkStatus) ";

    foreach($listOfCars as $eachCar){

        $values = "VALUES (". $eachCar->RONum . ", " . $eachCar->Priority . ", " .
                $eachCar->TechIndex . ", " . $eachCar->CarIndex . ", '" .
                $eachCar->Status . "')";

        if ($conn->query($tsql . $values) === TRUE) {
	      ; //echo $ro_num . " uploaded<br/>";
	    } else {
	      echo "Error: " . $tsql . $values . "<br>" . $conn->error;
	    }
    }

    $conn = null;

/*


    if($result === FALSE){
      die( print_r(sqlsrv_errors(), TRUE));
    } else {
       echo "Submission successful!";
      sqlsrv_free_stmt($result);
      sqlsrv_close($conn);
    }
*/
//    echo "Process post";
}


?>
