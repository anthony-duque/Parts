<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Utility_Scripts.php';
require 'Stage_Car_model.php';        // contains Car() model

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
                r.RONum, r.Loc_ID, ps.stage_ID,
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


function Process_GET($locID, $stageCount){

//    $update = $_GET["update"];
    class ProdStage{

        public $stageCars = [];

        function __construct($sc){
            $this->stageCars = $sc;
        }
    }   // prodStage{}


    $production_cars = [];

    for($stage = -1; $stage < $stageCount; ++$stage){
        $production_cars[$stage] = new Production_Stage($locID, $stage);
    }

    ComputePartsReceived($production_cars);

    echo json_encode(new ProdStage($production_cars));

}   //  Process_GET()

?>
