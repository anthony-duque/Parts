<?php

require('db_open.php');

$method = $_SERVER['REQUEST_METHOD'];

switch(strtoupper($method)){

    case 'GET':
        Process_GET($conn);
        break;

    case 'POST':
        Process_POST($conn);
        break;

    default:
        break;
}

//////////////////////////////////////////////////


class Estimator{

    public $name;
    public $locID;

    function __construct($rec){
        $this->name    = $rec["estimator"];
        $this->locID   = $rec["shop_id"];
    }
}   // Estimator{}


function Process_GET($db_conn){

        $sql = "";

        switch(true){

            case isset($_GET["repairID"]):

                $sql =  "SELECT estimator, shop_id " . 
                        "FROM repairs " . 
                        "WHERE repair_id = {$_GET['repairID']};";
                break;

            case isset($_GET["shopID"]):

                $sql =  "SELECT DISTINCT estimator, shop_id " . 
                        "FROM repairs" . 
                        "WHERE shop_id = {$_GET['shopID']};";
                break;

            case isset($_GET["companyID"]):

                $sql =  "SELECT DISTINCT r.estimator, r.shop_id " .
                        "FROM repairs r INNER JOIN shops s " .
                        " ON r.shop_id = s.id " .  
                        "WHERE s.company_id = {$_GET['companyID']};";
                break;                
                
            default:
                $sql =  "SELECT DISTINCT r.estimator, r.shop_id " .
                        "FROM repairs r";
                break;
        };

        try{

            $s = mysqli_query($db_conn, $sql);
            $estimator_list = [];

            while($r = mysqli_fetch_assoc($s)){
                array_push($estimator_list, new Estimator($r));
            }   // while()

        } catch(Exception $e){

            echo "Fetching Estimatorr List failed." . $e->getMessage();

        } finally {

            $conn = null;
            echo json_encode($estimator_list);

        }   // try-catch{}

    }   // function ProcessGET()

?>