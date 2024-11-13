<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
          ProcessPUT();
          break;

       case "GET":  // get cars on the Paint List
          $paintList = ProcessGET();
          echo json_encode($paintList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }

    function Get_Recepients($car_info){

        Employee{

            public $userName;
            public $cellNumber;
            public $cellService;
            public $deptCode;
            public $email;
            public $notify;
            public $notifPreference;

            function __construct($rec){

                $this->userName = $rec["userName"];
                $this->cellNumber = $rec["cellNumber"];
                $this->cellService = $rec["cellService"];
                $this->deptCode = $rec["deptCode"];
                $this->email = $rec["email"];
                $this->notify = $rec["notify"];
                $this->notifPreference = $rec["notif_preference"];
            }
        };

        var $recepients = [];

        // Get the technician's email or text
        // 1) Get the tech's first
        // 2) Lookup get the cell num or email address

        $USERNAME = trim($car_info->technician);
        $USERNAME = strtoupper($userName);

        $tsql = "SELECT * FROM Employee_Table WHERE UPPER(userName) = '$USERNAME'";

        require('db_open.php');

        $empRec = null;

        try {

            $s = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($s);
            $empRec = new CarInfo($r);

        } catch(Exception $e) {

            echo "Fetching Employee Info failed for $userName" . $e->getMessage();

        } finally {

            if (strlen($empRec->cellNumber) > 0){
                ;
            }

        }   // try-catch{}

        // Get the estimator's email of bind_textdomain_codeset

        $conn = null;
        return $car;

    }


    function Email_Status($old_status, $new_status){

        echo "<br/><br/>RO " . $new_status->RONum . " updated from " . $old_status->status . " to " . $new_status->Status;

        $test_mode = true;

        $subject    = "Paint List";

        $body           = "";
        $carName        = "";
        $car_name_arr   = "";

        $carInfo = GetCar($new_status->RONum);

        $car_name_arr = str_word_count($carInfo->vehicle, 1);

        switch(count($car_name_arr)){

        	case 1:
            case 2:
        		$carName = implode(" ", $car_name_arr);
                break;

        	default:
        		$carName = $car_name_arr[0] . " " . $car_name_arr[1] . " " . $car_name_arr[2];
                break;
        }

        $oldStatus = strtoupper($old_status->status);
        $newStatus = strtoupper($new_status->Status);

        $body = <<<emailMsg

                $carInfo->ro_num - $carInfo->owner [ $carName ]

                Changed Status
                    From: $oldStatus;
                      To: $newStatus;

        emailMsg;
        echo $body; // test

        $headers    = "From: Automated Email<donotreply@cityautobody.net>\r\n";

        if ($test_mode){
//            $to         = "8053778977@txt.att.net";
            $to = "8054282425@txt.att.net";
//            $to = Get_Recepients($carInfo);
//            $to = "somebody@example.com, somebodyelse@example.com"            $to         = "adduxe@hotmail.com";
            $headers    .= "Cc: Sonny<adduxe@gmail.com>";

        } else {

            $to         = "8053778977@txt.att.net";
            $headers    .= "Cc: Jim<adduxe@gmail.com>";
        }

        mail($to, $subject, $body, $headers);

    }   // Email_Status()


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

       } catch (Exception $e){
           echo "Failed to update paint list status for RO " . $newStat->RONum . $e->getMessage();
       }

       $conn = null;        // close the database

   }   // Update_Status()


    function RemoveFromPaintList($removedROs, $dbConn){

        $roList = implode(",", $removedROs);
        $tsql = "DELETE FROM Work_Queue WHERE Dept_Code = 'P' AND RO_Num IN (" . $roList . ")";
        //echo $tsql;

        if ($dbConn->query($tsql) === TRUE) {
    		echo "<br/>Deleted RO's " . $roList . " from Paint List.<br/>";
    	} else {
            echo "Error: " . $tsql . "<br> - " . $conn->error;
            exit;
    	}

    }   // RemoveFromPaintList()


    function UpdatePaintList($newList){

        $oldList = ProcessGET();

        $carsToBeAdded = [];

            // 1)  cycle through the new list.
            // 2)  if the ro is found on the old list, update it.
            // 3  if not, add it to the list
        foreach($newList as $newStatus){

            $new_RO_found = true;

            echo "\n $newStatus->RONum : \n";

            foreach($oldList as $oldStatus){
                if($newStatus->RONum == $oldStatus->ro_num){

                    echo "newStatus = $newStatus->Status | oldstatus = $oldStatus->status";
                    $new_RO_found = false;
                    if($newStatus->Status != $oldStatus->status){
                        Update_Status($oldStatus, $newStatus);
                    }
                }
            }

            if ($new_RO_found){
                    // these RO's need to be added to the list.
                array_push($carsToBeAdded, $newStatus);
            }
        }

        require('db_open.php');

        AddCarToPaintList($carsToBeAdded, $conn);
            // delete RO's in the paint list that are not in
            // the new list
        $old_ROs_to_be_removed = [];

        foreach($oldList as $oldStatus){

            $removed_RO_found = true;

            foreach($newList as $newStatus){

                if($newStatus->RONum == $oldStatus->ro_num){
                    $removed_RO_found = false;
                }
            }

            if($removed_RO_found){
                array_push($old_ROs_to_be_removed, $oldStatus->ro_num);
            }
        }

        if (count($old_ROs_to_be_removed) > 0){
            RemoveFromPaintList($old_ROs_to_be_removed, $conn);
        }

        $conn = null;
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

        require('db_open.php');

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

//        echo "SendNotifications";
//        $to         = "8053778977@txt.att.net";
        $to         = "adduxe@hotmail.com";
        $subject    = "Paint List";
        $headers    = "From: Automated Email<donotreply@cityautobody.net>\r\n";
//        $headers    .= "Cc: Jim<jimd@cityautobody.net>";
        $headers    .= "Cc: Parts<adduxe@gmail.com>";

        $body           = "";
        $carName        = "";
        $car_name_arr   = "";

        forEach($carList as $i => $eachCar){

            $carInfo = GetCar($eachCar->RONum);
            $priority = $i + 1;
            $status = strtoupper($eachCar->Status);

            $car_name_arr = str_word_count($carInfo->vehicle, 1);
//            echo var_dump($car_name_arr);

            switch(count($car_name_arr)){

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


    function AddCarToPaintList($newCarsList, $dbConn){

        $tsql = "INSERT INTO Work_Queue " .
                 "(RO_Num, Priority, Dept_Code, Status) ";

        foreach($newCarsList as $eachCar){

            $values = "VALUES (". $eachCar->RONum . ", " . $eachCar->Priority . ", '" .
                    $eachCar->DeptCode . "', '" .$eachCar->Status . "')";

            //echo $values . "<br/>";
            if ($dbConn->query($tsql . $values) === TRUE) {
              ;// echo $ro_num . " uploaded<br/>";
            } else {
              echo "Error: " . $tsql . $values . "<br>" . $conn->error;
            }
        }

    }   // AddCarToPaintList()


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
            AddCarToPaintList($listOfCars, $conn);
            SendNotification($listOfCars);
        }

        $conn = null;
    }   //   ProcessPOST()

?>
