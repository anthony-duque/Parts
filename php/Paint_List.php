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
//      BroadcastList($data);
      break;

   case "PUT":    // Could read from input and query string
   //         $qString = $_GET["id"];
   //         echo "PUT = " . $qString;
      echo 'PUT';
      $json = file_get_contents('php://input');
      $data = json_decode($json);
//      var_dump($data);
      break;

   case "GET":  // get cars on the Paint List
      $paintList = ProcessGET();
      echo json_encode($paintList);
      break;

   case "DELETE":
      echo 'DELETE';
      $qString = $_GET["id"];
      echo "DELETE = " . $qString;
      break;

   default:
      break;
}


function ProcessGET(){

    require('db_open.php');

    class Car {

        public $ro_num;
        public $owner;
        public $color;
        public $vehicle;
        public $estimator;
        public $technician;

        function __construct($rec){

            $this->ro_num       = $rec["RONum"];
            $this->owner        = ucwords(strtolower($rec["Owner"]));
            $this->vehicle      = $rec["Vehicle"];
            $this->estimator    = $rec["Estimator"];
            $this->color        = $rec["Vehicle_Color"];
            $this->technician   = $rec["Technician"];

        }   // Car($rec)
    }

    class List_Car {

        public $ro_num;
        public $priority;
        public $dept_code;
        public $status;

        function __construct ($rec){
            $this->ro_num       = $rec["RO_Num"];
            $this->priority     = $rec["Priority"];
            $this->dept_code    = $rec["Dept_Code"];
            $this->status       = $rec["Status"];
        }
    }

    $sql = <<<strSQL
                SELECT RO_Num, Priority, Dept_Code, Status
                FROM Work_Queue
                WHERE Dept_Code = 'P'
                ORDER BY Priority ASC
            strSQL;

    $carList = [];

    try {

        $listCar = null;

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){

            $listCar = new List_Car($r);
            $carList.push($listCar);
        }

    } catch(Exception $e) {

        echo "Fetching Paint List failed." . $e->getMessage();

    } finally {

        $conn = null;
        return $carList;

    }   // try-catch{}
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
