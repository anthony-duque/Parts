<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Allow-Control-Origin: *');

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            ProcessPOST($data);
            break;

       case "PUT":    // Could read from input and query string
            echo 'PUT';
            ProcessPUT();
            break;

       case "GET":  // get cars on the Paint List
            $shopID = $_GET["shopID"];
            $priorityList = ProcessGET($shopID);
            echo json_encode($priorityList);
            break;

       case "DELETE":
            echo "DELETE";
            $repairID = $_GET["repairID"];
            ProcessDELETE($repairID);
            break;

       default:
          break;
    }

////////////////////////////////////////////////

class PriorityCar{

    public $technician;
    public $repair_id;
    public $dept_code;
    public $priority;

    function __construct($rec){
        $this->technician   = $rec["technician"];
        $this->repair_id    = $rec["repair_id"];
        $this->dept_code    = $rec["dept_code"];
        $this->priority     = $rec["priority"];
    }   // construct()

}   // PriorityCar{}


function ProcessDELETE($repair_id){

    require 'db_open.php';

    $tsql = <<<strSQL
        DELETE FROM tech_car_priority
        WHERE repair_id = $repair_id
    strSQL;

    try{

        $conn->query($tsql);

    } catch(Exception $e){

        echo "Error: " . $tsql . "<br>" . $conn->error;

    } finally {

        $conn = null;

    }   // try-catch
}   // ProcessDELETE()


function ProcessGET($shop_id){

    require 'db_open.php';

    $sql = <<<strSQL
                SELECT tcp.repair_id, tcp.technician, tcp.priority, tcp.dept_code
                FROM tech_car_priority tcp INNER JOIN repairs r
                    ON tcp.repair_id = r.id
                WHERE r.shop_id = $shop_id
                ORDER BY tcp.priority ASC
            strSQL;

    try{

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


function ProcessPOST($car){

    //echo "pCar = " . var_dump($car);

    require 'db_open.php';

    $tsql = <<<strSQL

        INSERT INTO tech_car_priority
            (technician, priority, repair_id, dept_code)
        VALUES
            ('$car->technician', $car->priority, $car->repair_id, '$car->dept_code');
    strSQL;

    try{

        $conn->query($tsql);

    } catch(Exception $e){

        echo "Error: " . $tsql . "<br>" . $conn->error;

    } finally {

        $conn = null;

    }   // try-catch

}   // ProcessPOST()

?>
