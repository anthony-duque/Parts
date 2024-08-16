<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');

    $method = $_SERVER['REQUEST_METHOD'];

    //echo $method;

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
//          echo "POST";
//          var_dump($data);
//          ProcessPOST($data);
    //      BroadcastList($data);
          break;

       case "PUT":    // Could read from input and query string
       //         $qString = $_GET["id"];
       //         echo "PUT = " . $qString;
          echo 'PUT';
          ProcessPUT();
//          $json = file_get_contents('php://input');
//          var_dump($json);
//          parse_str($json, $data);
//          $data = json_decode($json);
//          var_dump($data);
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

    function Email_Status($old_status, $new_status){
        echo "<br/><br/>RO " . $new_status->RONum . " updated from " . $old_status->status . " to " . $new_status->Status;
    }


    function Update_Status($oldStat, $newStat)
    {

        require('db_open.php');

        $tsql = <<<strSQL
                UPDATE Work_Queue
                SET Status = '$newStat->Status'
                WHERE RO_Num = $newStat->RONum
                    AND Dept_Code = 'P';
            strSQL;

       //echo $strSQL;
       try{

            $result = $conn->query($tsql);
            echo "Car " . $newStat->RONum . " updated.";
            Email_Status($oldStat, $newStat);

       } catch (PDOException $pe){
           echo "Failed to update paint list status for RO " . $newStat->RONum . $pe->getMessage();
       }

       $conn = null;        // close the database

    }

    function UpdatePaintList($newList){

        $oldList = ProcessGET();

        foreach($newList as $newStatus){
            foreach($oldList as $oldStatus){
                if($newStatus->RONum == $oldStatus->ro_num){
                    if($newStatus->Status != $oldStatus->status){
                        Update_Status($oldStatus, $newStatus);
                    }
                }
            }
        }
    }   // UpdatePaintList()


    function ProcessPUT(){

        $putData = fopen("php://input", "r");

        $rawJson = "";

        while($data = fread($putData, 1024)){
            $rawJson .= $data;
        }

        fclose($putData);

        $jsonData = json_decode($rawJson);
        var_dump($jsonData);

        UpdatePaintList($jsonData);

    }   // ProcessPUT()

    function ProcessGET(){

        require('db_open.php');

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

            $eachEntry = null;

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $eachEntry = new List_Car($r);
                array_push($carList, $eachEntry);
            }

        } catch(Exception $e) {

            echo "Fetching Paint List failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $carList;

        }   // try-catch{}
    }


    class CarInfo{

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
    }   // Car{}

    function GetCar($roNum){

        $sql = <<<strSQL
                    SELECT SUBSTRING_INDEX(Technician, ' ', 1) AS Technician,
                        RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                        Vehicle, Estimator, Vehicle_Color
                    FROM Repairs
                    WHERE RONum = $roNum
                strSQL;
                //echo $sql;
        $car = null;

        require('db_open.php');

        try {

            $s = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($s);
            $car = new CarInfo($r);

        } catch(Exception $e) {

            echo "Fetching Car Info failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $car;

        }   // try-catch{}
    }   // GetCar()



    function SendNotification($carList){

        echo "SendNotifications";
        $to         = "8053778977@txt.att.net";
       $subject    = "Paint List";
        $headers    = "From: Automated Email<donotreply@cityautobody.net>\r\n";
        $headers    .= "Cc: Jim<parts@cityautobody.net>";

        $body           = "";
        $carName        = "";
        $car_name_arr   = "";

        forEach($carList as $i => $eachCar){

            $carInfo = GetCar($eachCar->RONum);
            $priority = $i + 1;
            $status = strtoupper($eachCar->Status);

            $car_name_arr = str_word_count($carInfo->vehicle, 1);
//            echo var_dump($car_name_arr);

            switch($car_name_arr.length){

            	case 1:
                case 2:
            		$carName = implode(" ", $car_name_arr);
                    break;

            	default:
            		$carName = $car_name_arr[0] . " " . $car_name_arr[1] . " " . $car_name_arr[2];
                    break;
            }


            $body .= <<<emailMsg

                    $priority ) $eachCar->RONum - $carInfo->owner
                    [ $carName ]
                    Status: $status

            emailMsg;
        }

        mail($to, $subject, $body, $headers);

    }   // SendNotification



    function ProcessPOST($listOfCars){

        require('db_open.php');

        $tsql = "DELETE FROM Work_Queue WHERE Dept_Code = 'P'";

    	if ($conn->query($tsql) === TRUE) {

    		echo "<br/>Work Queue Table cleared.<br/>";

    	} else {

    	  echo "Error: " . $tsql . "<br> - " . $conn->error;
    	  exit;
    	}

        if(count($listOfCars) > 0){

            $tsql = "INSERT INTO Work_Queue " .
                     "(RO_Num, Priority, Dept_Code, Status) ";

            foreach($listOfCars as $eachCar){

                $values = "VALUES (". $eachCar->RONum . ", " . $eachCar->Priority . ", '" .
                        $eachCar->DeptCode . "', '" .$eachCar->Status . "')";

                echo $values . "<br/>";
                if ($conn->query($tsql . $values) === TRUE) {
        	      ;// echo $ro_num . " uploaded<br/>";
        	    } else {
        	      echo "Error: " . $tsql . $values . "<br>" . $conn->error;
        	    }
            }

            SendNotification($listOfCars);
        }

        $conn = null;

    }   //   ProcessPOST()
?>
