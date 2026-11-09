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

      $jsonData = json-_decode($rawJson);
      //var_dump($jsonData);

      ProcessPUT($jsonData);
      */
      break;

   case "GET":  // get cars that are in production for a given location and stage
        $stages = explode(",", $_GET["stages"]);
        $shop_id = $_GET["shopID"];
        Process_GET($shop_id, $stages);
      break;

   default:
      break;

} // switch()

////////////////////////////

class Production_Stage {

    public $cars = [];

    function GetCars($shop_id, $stageCode){

        $strSQL = <<<sqlStmt

           SELECT
                
                r.id, r.ro_num, r.shop_id, r.stage_code,
                SUBSTRING_INDEX(r.estimator, ' ', 1) AS estimator,
                SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                r.vehicle, LCASE(r.vehicle_color) AS vehicle_color,
                SUBSTRING_INDEX(r.technician, ' ', 1) AS technician,
                r.vehicle_in, r.current_phase, r.scheduled_out, 
                r.insurance

            FROM repairs r

            WHERE r.shop_id = $shop_id AND r.stage_code = '$stageCode';
sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($conn, $r));
        }   // while()

        $conn = null;

        return $this->cars;
    }   // GetCars()

        // Get all the cars that are in production for each stage 
    function __construct($shop_id, $stage_code){
        $this->cars = $this->GetCars($shop_id, $stage_code);
    }
}   // Production_Stage {}


function Process_GET($shopID, $shopStages){

    class ProdStage{

        public $stageCars = [];
        public $last_upload_time;

        function __construct($shop_ID, $production_cars){

            $this->stageCars = $production_cars;
            $this->last_upload_time = Get_Upload_Time($shop_ID);
        }
    }   // prodStage{}


    $production_cars = [];

    foreach($shopStages as $stage){
        array_push($production_cars, new Production_Stage($shopID, $stage));
    }
    // 
    // for($stage = 0; $stage < $stageCount; ++$stage){
    //     $production_cars[$stage] = new Production_Stage($shopID, $stage);
    // }

    ComputePartsReceived($production_cars);

    echo json_encode(new ProdStage($shopID, $production_cars));

}   //  Process_GET()

?>
