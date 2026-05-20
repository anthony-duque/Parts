<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Utility_Scripts.php';
require 'Stage_Car_model.php';        // contains Car() model

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':
    /*
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
    */
      break;

   case "PUT":    // Could read from input and query string
    /*
      $putData = fopen("php://input", "r");
      $rawJson = "";

      while($data = fread($putData, 1024)){
          $rawJson .= $data;
      }
      fclose($putData);

      $jsonData = json_decode($rawJson);
      //var_dump($jsonData);

      ProcessPUT($jsonData);
      */
      break;

   case "GET":  // get cars that are in production for a given location and stage
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

               r.ro_num, r.loc_id, cs.stage_id,
                SUBSTRING_INDEX(r.estimator, ' ', 1) AS estimator,
                SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                r.vehicle, LCASE(r.vehicle_color) AS vehicle_color,
                SUBSTRING_INDEX(r.technician, ' ', 1) AS technician,
                r.vehicle_in, r.current_phase, r.scheduled_out, r.insurance

            FROM repairs r INNER JOIN car_stage cs
                    ON r.ro_num = cs.ro_num AND r.loc_id = cs.loc_id

            WHERE r.loc_id = $locID AND cs.stage_id = $stage_ID
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
        public $last_upload_time;

        function __construct($sc){
            $this->stageCars = $sc;
            $this->last_upload_time = Get_Upload_Time();
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
