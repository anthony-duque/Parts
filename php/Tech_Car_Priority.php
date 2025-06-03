<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
          ProcessPUT();
          break;

       case "GET":  // get cars on the Paint List
          $priorityList = ProcessGET();
          echo json_encode($priorityList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }

////////////////////////////////////////////////

class PriorityCar{

    public $roNum;
    public $locID;

    function __construct($rec){
        $this->roNum    = $rec["RO_Num"];
        $this->locID    = $rec["LocationID"];
    }
}


function ProcessGET(){

    $sql = <<<strSQL
                SELECT RO_Num, LocationID
                FROM Tech_Car_Priority
            strSQL;

    try{

    require 'db_open.php';

        $priorityCars = [];

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){
            array_push($priorityCars, new PriorityCar($r));
        }   // while()

    } catch(Exception $e){

        echo "Fetching Priority Cars List failed." . $e->getMessage();

    } finally {

        $conn = null;
         return $priorityCars;

    }   // try-catch{}

}   // ProcessGET()

?>
