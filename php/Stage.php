<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

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
        $stage_count = $_GET["stages_count"];
        $loc_id = $_GET["locID"];
      Process_GET($loc_id, $stage_count);
      break;

   default:
      break;

} // switch()

////////////////////////////

class Production_Stage {

    public $cars = [];

    function GetCars($locID, $stage_ID){

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
            WHERE r.Loc_ID = $locID AND ps.stage_ID = $stage_ID
sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($conn, $r));
        }   // while()

        $conn = null;

        return $this->cars;
    }   // GetCars()


    function __construct($locationID, $stageID){
        $this->cars = $this->GetCars($locationID, $stageID);
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
    public $received_quantity;

    function __construct($rec){
        $this->part_description     = $rec["Part_Description"];
        $this->vendor_name          = $rec["Vendor_Name"];
        $this->received_quantity    = $rec["Received_Qty"];
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
    public $subletS = [];
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
                    SELECT Part_Description, Vendor_Name, Received_Qty
                    FROM PartsStatusExtract
                    WHERE Part_Type = 'Sublet'
                    AND RO_Num = $this->ro_num AND Loc_ID = $this->locationID
                    ORDER BY Received_Qty
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


function Process_GET($locID, $stageCount){

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


    $production_cars = [];

    for($stage = 0; $stage < $stageCount; ++$stage){
        $production_cars[$stage] = new Production_Stage($locID, $stage);
    }

    ComputePartsReceived($production_cars);

    echo json_encode(new ProdStage($production_cars));

}   //  Process_GET()

?>
