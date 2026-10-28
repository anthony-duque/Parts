<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method     = $_SERVER['REQUEST_METHOD'];
$shopID  = $_GET["shopID"];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data, $shopID);
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
      $stage_headings = Process_GET($shopID);
      echo json_encode($stage_headings);
      break;

   default:
      break;

} // switch()

////////////////////////////


class Stage {

    public $code;
    public $description;
    public $sequence_num;

    function __construct($rec){

        $this->code         = $rec["stage_heading_code"];
        $this->description  = $rec["description"];
        $this->sequence_num = $rec["sequence_num"];
    }

}   // Stage{}


function Process_GET($shopID){

    $stages = [];

    $sql = <<<strSQL
                SELECT cs.sequence_num, cs.stage_heading_code, sh.description
                FROM shop_stage_headings cs INNER JOIN stages sh
                    ON cs.stage_heading_code = sh.code
                WHERE cs.shop_id = $shopID
                ORDER BY cs.sequence_num
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $sql);

    while($r = mysqli_fetch_assoc($s)){
        array_push($stages, new Stage($r));
    }   // while()

    $conn->close();
    return $stages;

}   //  Process_GET()


function ProcessPOST($shopHeadings, $shop_ID){

    $sql = "DELETE FROM shop_stage_headings WHERE shop_id = $shop_ID";

    require('db_open.php');

    $s = mysqli_query($conn, $sql);

    if (!$s){

        echo "Error deleting records: " . mysqli_error($conn);
    
    } else {

        $insertSQL = <<<strSQL
                INSERT INTO shop_stage_headings (shop_id, sequence_num, stage_heading_code)
                VALUES 
            strSQL;

        foreach($shopHeadings as $index => $stage){

            $insertSQL .= " ($shop_ID, $index, '$stage->code'),";

        }   //  foreach()
    
        $insertSQL = rtrim($insertSQL, ",") . ";";

        $s = mysqli_query($conn, $insertSQL);

        if($s){
            echo "Record added successfully";
        } else {
            echo "Error adding record: " . mysqli_error($conn);
        }
    }

    $conn->close();

}   //  ProcessPOST()

?>