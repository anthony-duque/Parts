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
        $shop_id = $_GET["shopID"];
        $stage_count = intval($_GET["stage_count"]);
        Process_GET($shop_id, $stage_count);
      break;

   default:
      break;

} // switch()

////////////////////////////

class Production_Stage {

    public $cars = [];

        // Get all the cars that are in production for each stage 
    function __construct($shop_id, $stageSequence){

        $strSQL = <<<sqlStmt

           SELECT
                r.id, r.ro_num, r.shop_id, r.stage_sequence,
                SUBSTRING_INDEX(r.estimator, ' ', 1) AS estimator,
                SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                r.vehicle, LCASE(r.vehicle_color) AS vehicle_color,
                SUBSTRING_INDEX(r.technician, ' ', 1) AS technician,
                r.vehicle_in, r.current_phase, r.scheduled_out, 
                r.insurance
            FROM repairs r
            WHERE r.shop_id = $shop_id AND r.stage_sequence = $stageSequence;

        sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($conn, $r));
        }   // while()

        $conn = null;

    }   // Production_Stage()

}   // Production_Stage {}


function Process_GET($shop_id, $stage_count){

    class ProdStage{

        public $stageCars = [];
        public $last_upload_time;

        function __construct($shopID, $stageCount){

            for($stage = 0; $stage < $stageCount; ++$stage){
                    array_push($this->stageCars, new Production_Stage($shopID, $stage));
            }
            
            $this->last_upload_time = Get_Upload_Time($shopID);

            ComputePartsReceived($this->stageCars);
        }
    
    }   // prodStage{}


    echo json_encode(new ProdStage($shop_id, $stage_count));

}   //  Process_GET()

?>