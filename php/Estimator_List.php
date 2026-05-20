<?php

require('db_open.php');

    class Estimator{

        public $name;
        public $locID;

        function __construct($rec){
            $this->name    = $rec["estimator"];
            $this->locID   = $rec["loc_id"];
        }
    }   // Estimator{}

    $sql = <<<strSQL

            SELECT DISTINCT

                SUBSTRING_INDEX(estimator, ' ', 1) AS estimator,
                loc_id

            FROM repairs

            WHERE estimator > ''

            ORDER BY estimator ASC
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
