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
            $priorityList = ProcessGET();
            echo json_encode($priorityList);
            break;

       case "DELETE":
            echo "DELETE";
            ProcessDELETE();
            break;

       default:
          break;
    }

////////////////////////////////////////////////

class PriorityCar{

    public $roNum;
    public $locID;
    public $priority;
    public $deptCode;
    public $technician;

    function __construct($rec){
        $this->roNum        = $rec["ro_num"];
        $this->locID        = $rec["location_id"];
        $this->priority     = $rec["priority"];
        $this->deptCode     = $rec["dept_code"];
        $this->technician   = $rec["technician"];
    }   // construct()

}   // PriorityCar{}


function ProcessDELETE(){

    require 'db_open.php';

    $ro     = $_GET["ro"];
    $tech   = $_GET["tech"];
    $locID  = $_GET["locID"];

    $tsql = <<<strSQL
        DELETE FROM tech_car_priority
        WHERE   ro_num = $ro AND
                technician = '$tech' AND
                location_id = $locID
    strSQL;

    try{

        $conn->query($tsql);

    } catch(Exception $e){

        echo "Error: " . $tsql . "<br>" . $conn->error;

    } finally {

        $conn = null;

    }   // try-catch
}   // ProcessDELETE()


function ProcessGET(){

    require 'db_open.php';

    $sql = <<<strSQL
                SELECT ro_num, location_id, priority, dept_code, technician
                FROM tech_car_priority
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
            (technician, ro_num, priority, location_id, dept_code)
        VALUES
            ('$car->technician', $car->roNum, $car->priority, $car->locationID, '$car->deptCode');
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
