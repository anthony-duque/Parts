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
      var_dump($jsonData);

      ProcessPUT($jsonData);
      break;

   case "GET":  // get cars on the Paint List
      $productionStages = [];
      for($stageID = CHECKINPRESCAN; $stageID <= READYFORDELIVERY; ++$stageID){
          $stageCars = new StageCars($stageID);
          $productionStages[$stageID] = $stageCars->cars;
      }
      echo json_encode($productionStages);
      break;

   case "DELETE":
      $qString = $_GET["id"];
      echo "DELETE = " . $qString;
      break;

   default:
      break;

} // switch()

////////////////////////////

/*
class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $color;
    public $estimator;
    public $locationID;
    public $currentPhase;
    public $stageID;

    function __construct($rec){
        $this->ro_num   = $rec["RONum"];
        $this->owner    = toProperCase($rec["Owner"]);
        $this->vehicle  = toProperCase($rec["Vehicle"]);
        $this->color    = toProperCase($rec["Vehicle_Color"]);
        $this->technician = toProperCase($rec["Technician"]);
        $this->estimator  = toProperCase($rec["Estimator"]);
        $this->locationID = $rec["Loc_ID"];
        $this->currentPhase = $rec["CurrentPhase"];
        $this->stageID    = $rec["stage_ID"];
    }   // constructor()
}  // class Car{}
*/

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
    public $parts_unordered;
    public $parts_waiting;
    public $parts_received;
    public $parts_returned;
    public $parts_percent;
    public $scheduled_out;
    public $locationID;
    public $stageID;

    function __construct($rec){

        $this->ro_num           = $rec["RONum"];
        $this->owner            = ucwords(strtolower($rec["Owner"]));
        $this->vehicle          = $rec["Vehicle"];
        $this->vehicle_color    = $rec["Vehicle_Color"];
        $this->vehicle_in       = $rec["Vehicle_In"];
        $this->technician       = $rec["Technician"];
        $this->estimator        = $rec["Estimator"];
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
        $this->stageID          = $rec["stage_ID"];

    }   // Car($rec)
}   // Car{}


class StageCars{

    public $cars = [];

    function GetCars($stage_ID){

        $strSQL = <<<sqlStmt
	               SELECT
                        r.RONum, r.Location, r.Loc_ID, ps.stage_ID,
                        SUBSTRING_INDEX(r.Estimator, ' ', 1) AS Estimator,
                        SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner,
                        r.Vehicle, LCASE(r.Vehicle_Color) AS Vehicle_Color,
                        SUBSTRING_INDEX(r.Technician, ' ', 1) AS Technician,
                        r.Vehicle_In, r.CurrentPhase, r.Scheduled_Out
                    FROM Repairs r INNER JOIN Production_Stage ps
                            ON r.RONum = ps.ro_Num AND r.Loc_ID = ps.loc_ID
                    WHERE ps.stage_ID = $stage_ID
sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($r));
        }   // while()

        $conn = null;

        return $this->cars;
    }   // function()

    function __construct($sID){
        $this->cars = $this->GetCars($sID);
    }   // function __construct()

}   // ProductionStage{}


function ProcessPUT($carObj){

    require('db_open.php');

    $tsql = <<<strSQL
            UPDATE Production_Stage
            SET stage_ID = $carObj->stage_ID
            WHERE ro_Num = $carObj->ro_Num
                AND loc_ID = $carObj->loc_ID
        strSQL;

    try{

        $result = $conn->query($tsql);
        echo "Car " . $carObj->ro_Num . " stage updated.";

    } catch (Exception $e){
        echo "Failed to update stage status for RO " . $carObj->ro_Num . $e->getMessage();
    }

    $conn = null;        // close the database connection

}   // ProcessPUT()

?>
