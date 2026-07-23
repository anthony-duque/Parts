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
          ProcessPOST($_POST, $companyID);
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


    function ProcessPOST($shop_arrays, $company_id)
    {
        $db_shops   = json_decode($shop_arrays["dbShops"]);
        $csv_shops  = json_decode($shop_arrays["csvShops"]);
        $shopList   = json_decode($shop_arrays["shopList"]);

        require('../db_open.php');

        try{

            foreach($shopList as $i){

                if ($db_shops[$i] !== $csv_shops[$i]){

                    $sql = "UPDATE locations " . 
                            "SET location = '$csv_shops[$i]' " . 
                            "WHERE company_id = $company_id " . 
                            " AND location = '$db_shops[$i]';";

                    $result = mysqli_query($conn, $sql);

                    echo "Shop name: '$db_shops[$i]' changed to '$csv_shops[$i]'<br/>";
                }   // if ($db_shops...)

            }   // foreach

            echo "<br/>Loading values from the extract table to repairs and parts tables.<br/>";

            $stmt = $conn->prepare("CALL sp_Load_Values_From_Extract_Table(?)");
            $stmt->bind_param("i", $company_id);
            $stmt->execute();

        }catch(Exception $e){

            echo "Updating Shop Names in location_id failed. (" . $e->getMessage() . ")";
        
        }finally{

            echo "<br/>Shop names successfully updated!<br/>";
            $conn->close();

        }   // try-catch

    }   // function ProcessPOST()


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

            }   // Get_Shops()


            function __construct($comp_ID){

                require('../db_open.php');

                $this->dbConn = $conn;

                $field = "location";
                $query = "SELECT $field FROM locations " .
                        "WHERE company_id = $comp_ID " .
                        "ORDER BY $field";
                
                $this->db_shops = $this->Get_Shops($query, $field);

                $field = "shop_name";
                $query = "SELECT DISTINCT $field FROM extract_file_dump " .
                        "WHERE company_id = $comp_ID " .
                        "ORDER BY $field";

                $this->csv_shops = $this->Get_Shops($query, $field);

                $this->dbConn->close();

            }   // __construct()
        
        }   // Shops{}

        return new Shops($company_id);

    }   // function ProcessGET()

?>