<?php

require('db_open.php');

    class Estimator{

        public $name;
        public $locID;

        function __construct($rec){
            $this->name    = $rec["Estimator"];
            $this->locID   = $rec["Loc_ID"];
        }
    }   // Estimator{}

    $sql = <<<strSQL
            SELECT DISTINCT
                SUBSTRING_INDEX(Estimator, ' ', 1) AS Estimator,
                Loc_ID
            FROM Repairs
            WHERE Estimator > '' AND RONum <> 1004
            ORDER BY Estimator ASC
        strSQL;

    try{

        $s = mysqli_query($conn, $sql);
        $estim_list = [];

        while($r = mysqli_fetch_assoc($s)){
            array_push($estim_list, new Estimator($r));
        }   // while()

    } catch(Exception $e){

        echo "Fetching Estimator List failed." . $e->getMessage();

    } finally {

        $conn = null;
        echo json_encode($estim_list);

    }   // try-catch{}

?>
