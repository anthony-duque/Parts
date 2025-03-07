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
          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
          ProcessPUT();
          break;

       case "GET":
          $materialsList = ProcessGET();
          echo json_encode($materialsList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }


    class material {

        public $part_number;
        public $brand;
        public $description;
        public $unit;
        public $type;
        public $reorder_qty;
        public $ordered_qty;    // not pulled from DB. Value is set in UI.

        function __construct($rec){
            $this->part_number  = $rec["Part_Number"];
            $this->brand        = $rec["Brand"];
            $this->description  = $rec["Description"];
            $this->unit         = $rec["Unit"];
            $this->type         = $rec["Type"];
            $this->reorder_qty  = $rec["Reorder_Quantity"];
            $this->ordered_qty  = 0;
        }
    }   // material{}


    function ProcessGET(){

        require('db_open.php');

        $sql = <<<strSQL
                    SELECT Part_Number, Brand, Description, Unit, Type, Reorder_Quantity
                    FROM Materials
                    ORDER BY Type, Description
                strSQL;

        $matList = [];

        try {

            $eachMat = null;
            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $eachMat = new material($r);
                //echo $eachMat;
                array_push($matList, $eachMat);
            }

        } catch(Exception $e) {

            echo "Fetching Materials List failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $matList;
        }   // try-catch{}

    }   // ProcessGET()

    function ProcessPOST($newMaterial){

        require('db_open.php');

        $tsql = <<<strSQL
                INSERT INTO Materials
                    (Part_Number,
                    Brand,
                    Description,
                    Unit,
                    Type,
                    Reorder_Quantity)
                VALUES
                    ('$newMaterial->part_number',
                    '$newMaterial->brand',
                    '$newMaterial->description',
                    '$newMaterial->unit',
                    '$newMaterial->type',
                    $newMaterial->reorder_qty);
            strSQL;

            if ($conn->query($tsql) === TRUE) {
               echo '(' . $newMaterial->part_number . ') ' .
                    $newMaterial->description . " uploaded<br/>";
            } else {
              echo "Error: " . $tsql . "<br>" . $conn->error;
            }

    }   // ProcessPOST()

//phpinfo();
?>
