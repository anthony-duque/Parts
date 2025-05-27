<?php

class PriorityCar{

    public $roNum;
    public $locID;

    function __construct($rec){
        $this->roNum    = $rec["RO_Num"];
        $this->locID    = $rec["LocationID"];
    }
}

//$roNums = [4326, 4497, 4606, 4267, 4297];
///$strROs = implode(',', $roNums);


$sql = <<<strSQL
            SELECT RO_Num, LocationID
            FROM Tech_Car_Priority
        strSQL;

try{

require 'db_open.php';

    $priorityCars = [];

    $s = mysqli_query($conn, $sql);

    while($r = mysqli_fetch_assoc($s)){
        array_push($priorityCars, new PriorityCar($r));
    }   // while()

} catch(Exception $e){

    echo "Fetching Priority Cars List failed." . $e->getMessage();

} finally {

    $conn = null;
    echo json_encode($priorityCars);

}   // try-catch{}
?>
