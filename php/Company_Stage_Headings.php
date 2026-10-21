<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method     = $_SERVER['REQUEST_METHOD'];
$companyID  = $_GET["companyID"];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data, $companyID);
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
      $stage_headings = Process_GET($companyID);
      echo json_encode($stage_headings);
      break;

   default:
      break;

} // switch()

////////////////////////////


class Stage {

    public $heading_id;
    public $sequence_num;
    public $description;

    function __construct($rec){

        $this->heading_id   = $rec["stage_heading_id"];
        $this->description  = $rec["description"];
        $this->sequence_num = $rec["sequence_num"];
    }

}   // Heading{}


function Process_GET($cID){

    $stages = [];

    $sql = <<<strSQL
                SELECT cs.sequence_num, cs.stage_heading_id, sh.description
                FROM company_stage_headings cs INNER JOIN stage_headings sh
                    ON cs.stage_heading_id = sh.id
                WHERE cs.company_id = $cID
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


function ProcessPOST($companyHeadings, $company_ID){

    $sql = "DELETE FROM company_stage_headings WHERE company_id = $company_ID";

    require('db_open.php');

    $s = mysqli_query($conn, $sql);

    if (!$s){

        echo "Error deleting records: " . mysqli_error($conn);
    
    } else {

        $insertSQL = <<<strSQL
                INSERT INTO company_stage_headings (company_id, sequence_num, stage_heading_id)
                VALUES 
            strSQL;

        foreach($companyHeadings as $index => $stage){

            $insertSQL .= " ($company_ID, $index, $stage->heading_id),";

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

}   //  Process_POST()

?>