<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
//          echo $json;
//          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
          ProcessPUT();
          break;

       case "GET":
          $matTypeList = ProcessGET();
          echo json_encode($matTypeList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }


    class MaterialType {

        public $code;
        public $description;

        function __construct($rec){
            $this->code = $rec["Code"];
            $this->description = $rec["Description"];
        }
    }   // Car{}


    function ProcessGET(){

        require('db_open.php');

        $sql = <<<strSQL
                    SELECT Code, Description
                    FROM Material_Types
                    ORDER BY Code
                strSQL;

        $typeList = [];

        try {

            $eachType = null;
            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $eachType = new MaterialType($r);
                //echo $eachMat;
                array_push($typeList, $eachType);
            }

        } catch(Exception $e) {

            echo "Fetching Material Type List failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $typeList;
        }   // try-catch{}
    }   // ProcessGET()


    function ProcessPOST(){


    }   // ProcessPOST()

//phpinfo();
?>
