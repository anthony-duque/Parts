<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $method = $_SERVER['REQUEST_METHOD'];
    $companyID = $_GET["companyID"];

    switch($method){

       case 'POST':;
//          $json = file_get_contents('php://input');
//          $data = json_decode($json);
//          echo $json;
//          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
//          echo 'PUT';
//          ProcessPUT();
          break;

       case "GET":
          $shops = ProcessGET($companyID);
          echo json_encode($shops, JSON_INVALID_UTF8_SUBSTITUTE);
          break;

       case "DELETE":
//          $qString = $_GET["id"];
//          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }   // switch()


    function ProcessGET($company_id){

        class Shops {

            private $dbConn;
            public $db_shops = [];
            public $csv_shops = [];
            
            function Get_Shops($sql, $field){

                $result = mysqli_query($this->dbConn, $sql);

                $shops = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $shops[] = $row[$field];
                }

                return $shops;

            }   // Get_DB_Shops()


            function __construct($comp_ID){

                require('../db_open.php');

                $this->dbConn = $conn;

                $field = "shop_name";
                $query = "SELECT $field FROM location_ids " .
                        "WHERE company_id = $comp_ID " .
                        "ORDER BY $field";
                
                $this->db_shops = $this.Get_Shops($query, $field);

                $field = "shop_name";
                $query = "SELECT DISTINCT $field FROM extract_file_dump " .
                        "WHERE company_id = $comp_ID " .
                        "ORDER BY $field";

                $this->csv_shops = $this.Get_Shops($query, $field);

                $this->dbConn->close();

            }   // __construct()
        
        }   // Shops{}

        return new Shops($company_id);

    }   // function ProcessGET()

?>