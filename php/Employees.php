<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    case 'POST':
//        echo 'PUT';
        break;

    case "PUT":    // Could read from input and query string
//        echo 'PUT';
        break;

    case "GET":  // get cars on the Paint List
//        echo 'GET';
        $roles = $_GET["roles"];
        $list = Process_GET($roles);
        echo json_encode($list);
        break;

    case "DELETE":
        break;

    default:
        break;

} // switch()


class employee{

    public $firstName;
    public $locID;

    function __construct($rec){
        $this->firstName = $rec["firstName"];
        $this->locID = $rec["locID"];
    }
}   // employee


function Process_GET($roleCodes){

    $deptCodes = implode("','", $roleCodes);
    $deptCodes = "'" . $deptCodes . "'";
    $tsql = <<<strSQL
                SELECT firstName, locID
                FROM Employee_Table
                WHERE deptCode IN ($deptCodes);
            strSQL;
//    echo $tsql;

    require('db_open.php');

    try{

        $s = mysqli_query($conn, $tsql);
        $empList = [];

        while($r = mysqli_fetch_assoc($s)){
            array_push($empList, new employee($r));
        }   // while()

    } catch(Exception $e){

        echo "Fetching employee list failed." . $e->getMessage();

    } finally {

        $conn = null;
        return $empList;

    }   // try-catch{}

}   // Process_GET()

?>
