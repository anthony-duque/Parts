<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
      break;

   case "PUT":    // Could read from input and query string
//      echo 'PUT';
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
//        echo "Email:" . $to;
    } else {

        $to = Get_Email_Address('ESTIMATOR', $car->locationID, $car->estimator);
//        echo $to;
//        $headers    .= "Cc: Jim<JimD@cityautobody.net>," . Get_Email_Address('PARTS', $car->locationID);
//        echo $headers;
    }

    mail($to, $subject, $body, $headers);
}


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
//        if (($carObj->parts_percent < 100) &&
//            ($carObj->stageID == FOR_PAINT))
//        {
//            echo $carObj->ro_num . " => Stage: " . $carObj->stageID . " =>  Parts:" . $carObj->parts_percent . " %";
//            Notify_Estimator($carObj);
//        }
    }

}   // ProcessPUT()

?>
