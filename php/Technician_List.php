<?php

require('db_open.php');

    class Technician{

        public $name;
        public $locID;

        function __construct($rec){
            $this->name    = $rec["technician"];
            $this->locID   = $rec["loc_id"];
        }
    }   // Estimator{}

    $sql = <<<strSQL

            SELECT DISTINCT

                SUBSTRING_INDEX(technician, ' ', 1) AS technician,
                loc_id

            FROM repairs
            
            WHERE technician > ''
            
            ORDER BY technician ASC

        strSQL;

    try{

        $s = mysqli_query($conn, $sql);
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

?>