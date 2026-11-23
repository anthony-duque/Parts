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


class Technician{

    public $name;
    public $locID;

    function __construct($rec){
        $this->name    = $rec["technician"];
        $this->locID   = $rec["shop_id"];
    }
}   // Estimator{}


function Process_GET($db_conn){

        $sql = "";

        switch(true){

            case isset($_GET["repairID"]):

                $sql =  "SELECT technician, shop_id " . 
                        "FROM repairs " . 
                        "WHERE repair_id = {$_GET['repairID']};";
                break;

            case isset($_GET["shopID"]):

                $sql =  "SELECT DISTINCT technician, shop_id " . 
                        "FROM repairs" . 
                        "WHERE shop_id = {$_GET['shopID']};";
                break;

            case isset($_GET["companyID"]):

                $sql =  "SELECT DISTINCT r.technician, r.shop_id " .
                        "FROM repairs r INNER JOIN shops s " .
                        " ON r.shop_id = s.id " .  
                        "WHERE s.company_id = {$_GET['companyID']};";
                break;                
                
            default:
                $sql =  "SELECT DISTINCT r.technician, r.shop_id " .
                        "FROM repairs r";
                break;
        };

        try{

            $s = mysqli_query($db_conn, $sql);
            $tech_list = [];

            while($r = mysqli_fetch_assoc($s)){
                array_push($tech_list, new Technician($r));
            }   // while()

        } catch(Exception $e){

            echo "Fetching Technician List failed." . $e->getMessage();

        } finally {

            $conn = null;
            echo json_encode($tech_list);

        }   // try-catch{}

    }   // function ProcessGET()




?>