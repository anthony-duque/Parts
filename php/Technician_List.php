<?php

require('db_open.php');

    class Technician{

        public $name;
        public $locID;

        function __construct($rec){
            $this->name    = $rec["Technician"];
            $this->locID   = $rec["Loc_ID"];
        }
    }   // Estimator{}

    $sql = <<<strSQL
            SELECT DISTINCT
                SUBSTRING_INDEX(Technician, ' ', 1) AS Technician,
                Loc_ID
            FROM Repairs
            WHERE Technician > '' AND RONum <> 1004
            ORDER BY Technician ASC
        strSQL;

    try{

        $s = mysqli_query($conn, $sql);
        $tech_list = [];

        while($r = mysqli_fetch_assoc($s)){
            array_push($tech_list, new Technician($r));
        }   // while()

    } catch(Exception $e){

        echo "Fetching Technicia List failed." . $e->getMessage();

    } finally {

        $conn = null;
        echo json_encode($tech_list);

    }   // try-catch{}

?>
