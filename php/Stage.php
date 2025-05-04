<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

const CHECKINPRESCAN    = 0;
const DISSEMBLY         = 1;
const REPAIR_PLAN       = 2;
const PENDING_APPROVAL  = 3;
const WAITINGFORPARTS   = 4;
const BODY_WORK         = 5;
const FOR_PRIMER        = 6;
const FOR_PAINT         = 7;
const DELAYED           = 8;
const REASSEMBLY        = 9;
const SUBLET            = 10;
const FOR_DETAIL        = 11;
const FINALQC_POSTSCAN  = 12;
const READYFORDELIVERY  = 13;

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
      break;

   case "PUT":    // Could read from input and query string
      echo 'PUT';
      $putData = fopen("php://input", "r");
      $rawJson = "";

      while($data = fread($putData, 1024)){
          $rawJson .= $data;
      }
      fclose($putData);

      $jsonData = json_decode($rawJson);
      //var_dump($jsonData);

      ProcessPUT($jsonData);
      break;

   case "GET":  // get cars on the Paint List
      Process_GET();
      break;

   default:
      break;

} // switch()

////////////////////////////

class Production_Stage {

    public $cars = [];

    function GetCars($stage_ID){

        $strSQL = <<<sqlStmt
           SELECT
                r.RONum, r.Location, r.Loc_ID, ps.stage_ID,
                SUBSTRING_INDEX(r.Estimator, ' ', 1) AS Estimator,
                SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner,
                r.Vehicle, LCASE(r.Vehicle_Color) AS Vehicle_Color,
                SUBSTRING_INDEX(r.Technician, ' ', 1) AS Technician,
                r.Vehicle_In, r.CurrentPhase, r.Scheduled_Out, Insurance
            FROM Repairs r INNER JOIN Car_Stage ps
                    ON r.RONum = ps.ro_Num AND r.Loc_ID = ps.loc_ID
            WHERE ps.stage_ID = $stage_ID
sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($conn, $r));
        }   // while()

        $conn = null;

        return $this->cars;
    }   // GetCars()


    function __construct($stageID){
        $this->cars = $this->GetCars($stageID);
    }
}   // Production_Stage {}


class Part{

    public $ro_quantity;
    public $ordered_quantity;
    public $received_quantity;
    public $returned_quantity;
    public $part_status;

    function __construct($rec){

        $this->ro_quantity       = $rec["RO_Qty"];
        $this->ordered_quantity  = $rec["Ordered_Qty"];
        $this->received_quantity = $rec["Received_Qty"];
        $this->returned_quantity = $rec["Returned_Qty"];
        $this->part_status       = ComputePartStatus(
                                        $this->ro_quantity,
                                        $this->ordered_quantity,
                                        $this->received_quantity,
                                        $this->returned_quantity
                                    );
    }   // Part()
}   // Part{}


class Sublet{

    public $part_description;
    public $vendor_name;

    function __construct($rec){
        $this->part_description = $rec["Part_Description"];
        $this->vendor_name      = $rec["Vendor_Name"];
    }
}   // Sublet{}


class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $vehicle_color;
    public $vehicle_in;
    public $current_phase;
    public $technician;
    public $estimator;
    public $parts = [];
    public $sublet = [];
    public $parts_unordered;
    public $parts_waiting;
    public $parts_received;
    public $parts_returned;
    public $parts_percent;
    public $scheduled_out;
    public $locationID;
    public $insurance;
    public $stageID;


    function Get_Sublet_List($dbConn){

        $sublets = [];

        $sql =  <<<strSQL
                    SELECT Part_Description, Vendor_Name
                    FROM PartsStatusExtract
                    WHERE Part_Type = 'Sublet'
                        AND RO_Num = $this->ro_num
                        AND Loc_ID = $this->locationID
                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($sublets, new Sublet($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Sublet List failed.";
        }   // try-catch

        return $sublets;
    }   // Get_Sublet_List()


    function Get_Parts_List($dbConn){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty
                    FROM PartsStatusExtract
                    WHERE Part_Number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (Part_Number > '' OR Vendor_Name > '')
                        AND Vendor_Name NOT LIKE '**%'
                        AND Part_Number NOT LIKE 'Aftermarket%'
                        AND (Part_Type <> 'Sublet')
                        AND RO_Num = $this->ro_num
                        AND Loc_ID = $this->locationID
                    ORDER BY Ordered_Qty ASC
                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching List of Parts failed.";
        }   // try-catch

        return $allParts;
    }   // GetAllParts()


    function __construct($dbConn, $rec){

        $this->ro_num           = $rec["RONum"];
        $this->owner            = toProperCase($rec["Owner"]);
        $this->vehicle          = toProperCase($rec["Vehicle"]);
        $this->vehicle_color    = $rec["Vehicle_Color"];
        $this->vehicle_in       = $rec["Vehicle_In"];
        $this->technician       = toProperCase($rec["Technician"]);
        $this->estimator        = toProperCase($rec["Estimator"]);
        $this->current_phase    = $rec["CurrentPhase"];
        $this->parts_unordered  = 0;
        $this->parts_waiting    = 0;
        $this->parts_received   = 0;
        $this->parts_returned   = 0;
        $this->parts_percent    = 0;
        $this->scheduled_out    = GetDisplayDate($rec["Scheduled_Out"]);
        $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
        $this->location         = $rec["Location"];
        $this->locationID       = $rec["Loc_ID"];
        $this->insurance        = $rec["Insurance"];
        $this->stageID          = $rec["stage_ID"];
        $this->parts            = $this->Get_Parts_List($dbConn);
        $this->sublet           = $this->Get_Sublet_List($dbConn);

    }   // Car($rec)
}   // Car{}


function Notify_Estimator($car)
{
    echo "<br/><br/>RO " . $car->ro_num . " notify " . $car->estimator;

    $test_mode = false;

    $subject    = $car->ro_num . " ($car->owner) Possible parts issues";

    $body       = "";

    $body = <<<emailMsg

            $car->ro_num - $car->owner [ $car->vehicle ]

            is already in Paint. But there may still be possible issues with parts.

    emailMsg;

    echo $body; // test

//    $headers = 'MIME-Version: 1.0';
//    $headers .= 'Content-type: text/html; charset=iso-8859-1';
    $headers = "From: Automated Email<donotreply@cityautobody.net>\r\n";

    if ($test_mode){
//            $to = "8053778977@txt.att.net";
//        $to = "8054282425@txt.att.net";
//            $to = "somebody@example.com, somebodyelse@example.com"            $to         = "adduxe@hotmail.com";
        $headers    .= "Cc: Sonny<adduxe@gmail.com>";
        echo "Email:" . $to;
    } else {

        $to = Get_Email_Address('ESTIMATOR', $car->locationID, $car->estimator);
//        echo $to;
//        $headers    .= "Cc: Jim<JimD@cityautobody.net>," . Get_Email_Address('PARTS', $car->locationID);
//        echo $headers;
    }

    mail($to, $subject, $body, $headers);
}


function Process_GET(){

//    $update = $_GET["update"];
    class ProdStage{

        public $timeStamp;
        public $stageCars = [];

        function GetTimeStamp(){

            require('db_open.php');

            $last_upload_date = '';

            $sql = "SELECT value FROM Adhoc_Table WHERE name = 'LAST_UPLOAD'";

            try{

                $s = mysqli_query($conn, $sql);
                $r = mysqli_fetch_assoc($s);
                $last_upload_date = $r["value"];

            } catch (Exception $e){

                echo "Fetching 'Last Update' value failed." . $e->getMessage();
            }   // catch()

            finally {
                $conn = null;
                return $last_upload_date;
            }   // class{}
        }   // GetTimeStamp()

        function __construct($sc){
            $this->timeStamp = $this->GetTimeStamp();
            $this->stageCars = $sc;
        }
    }   // prodStage{}


    $prod_stages = [];

    for($stageID = CHECKINPRESCAN; $stageID <= READYFORDELIVERY; ++$stageID){
        $prod_stages[$stageID] = new Production_Stage($stageID);
    }

    ComputePartsReceived($prod_stages);

    echo json_encode(new ProdStage($prod_stages));

}   //  Process_GET()


function ProcessPUT($carObj)
{

    require('db_open.php');

    $tsql = <<<strSQL
            UPDATE Car_Stage
            SET stage_ID = $carObj->stageID
            WHERE ro_Num = $carObj->ro_num
                AND loc_ID = $carObj->locationID
        strSQL;

    try{

        $result = $conn->query($tsql);
        echo "Car " . $carObj->ro_num . " stage updated.";

    } catch (Exception $e){

        echo "Failed to update stage status for RO " . $carObj->ro_Num . $e->getMessage();

    } finally {

        $conn = null;        // close the database connection
        if (($carObj->parts_percent < 100) &&
            ($carObj->stageID == FOR_PAINT))
        {
            echo $carObj->ro_num . " => Stage: " . $carObj->stageID . " =>  Parts:" . $carObj->parts_percent . " %";
            Notify_Estimator($carObj);
        }
    }

}   // ProcessPUT()

?>
