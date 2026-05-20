<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$vendorParts = Get_Parts_By_Vendor_Estimator();
echo json_encode($vendorParts);

///////////////////////////////

class Part{

    public $number;
    public $description;
    public $type;
    public $quantity;
    public $status;

    function __construct($rec){

        $this->number       = $rec["Part_Number"];
        $this->description  = $rec["Part_Description"];
        $this->type         = $rec["Part_Type"];
        $this->quantity     = $rec["RO_Qty"];
        $this->status       = $rec["Part_Status"];

    }   // __construct()
}   // Part{}


class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $parts = [];

    private function Get_Parts_By_Car($loc_id, $db_conn){

        $sql = <<<strSQL

                SELECT part_description, part_number, part_type,
                    ro_qty, part_status

                FROM parts_status

                WHERE TRIM(vendor_name) NOT LIKE '*%IN%HOUSE%'
                    AND part_type NOT IN ('Sublet', 'FIX ME')
                    AND loc_id = $loc_id AND ro_num = $this->ro_num
                    AND part_status IN ('NOT ORDERED', 'ORDERED')
            strSQL;

        try {

            $s = mysqli_query($db_conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($this->parts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Parts List failed.";
        }   // try-catch

    }   // Get_Parts_By_Car()
    function __construct($rec, $locID, $dbConn){

        $this->ro_num   = $rec["RO_Num"];
        $this->vehicle  = $rec["Vehicle"];
        $this->owner    = $rec["Owner"];
        $this->Get_Parts_By_Car($locID, $dbConn);
    }   // __construct()

}   // Car{}


class Estimator{

    public $name;
    public $cars = [];

    private function Get_Estimator_Cars($loc_id, $vend_name, $db_conn){

        if (empty($vend_name)){
            $vendorNameCheck = "pse.Vendor_Name = ''";
        } else {
            $vendorNameCheck = "pse.Vendor_Name = '$vend_name'";
        }

        $sql = <<<strSQL

                SELECT DISTINCT pse.ro_num, pse.vehicle, r.Owner

                FROM repairs r INNER JOIN parts_status pse
                    ON r.Loc_ID = pse.loc_id AND r.RONum = pse.ro_num

                WHERE
                        TRIM(pse.Vendor_Name) NOT LIKE '*%IN%HOUSE%'
                        AND pse.part_type NOT IN ('Sublet', 'FIX ME')
                        AND r.Loc_ID = $loc_id AND r.Estimator = '$this->name'
                        AND pse.part_status IN ('NOT ORDERED', 'ORDERED')
                        AND $vendorNameCheck

                ORDER BY pse.ro_num

            strSQL;

        try {

            $s = mysqli_query($db_conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                array_push($this->cars, new Car($r, $loc_id, $db_conn));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching cars failed.";
        }   // try-catch

    }   // Get_Estimator_Cars()

    function __construct($rec, $locID, $vendName, $dbConn){

        $this->name = $rec["Estimator"];
        $this->Get_Estimator_Cars($locID, $vendName, $dbConn);

    }   // construct()
}   // class Estimator{}


class Vendor{

    public $name;
    public $locID;
    public $estimators = [];

        // Get all the Estimators for this vendor
    private function Get_Vendor_Estimators($db_conn){

        if (empty($this->name)){
            $vendorNameCheck = "pse.Vendor_Name = ''";
        } else {
            $vendorNameCheck = "pse.Vendor_Name = '$this->name'";
        }

        $sql = <<<strSQL

            SELECT DISTINCT Estimator

            FROM repairs r INNER JOIN parts_status pse
                ON r.Loc_ID = pse.loc_id AND r.RONum = pse.ro_num

            WHERE

                TRIM(pse.vendor_name) NOT LIKE '*%IN%HOUSE%'
                AND pse.part_type NOT IN ('Sublet', 'FIX ME')
                AND r.Loc_ID = $this->locID
                AND pse.part_status IN ('NOT ORDERED', 'ORDERED')
                AND $vendorNameCheck

           ORDER BY r.Estimator
        strSQL;

//        echo $sql;
//        exit;

        try {

            $s = mysqli_query($db_conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($this->estimators, new Estimator($r, $this->locID, $this->name, $db_conn));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Estimator List failed.";
        }   // try-catch

    }   // Get_Estimators()

    function __construct($rec, $dbConn){
        $this->name = $rec["vendor_name"];
        $this->locID = $rec["loc_id"];
        $this->Get_Vendor_Estimators($dbConn);
    }   // __construct()

}   // Vendor{}

//////////////////////////////////////////////////////////

function Get_Parts_By_Vendor_Estimator(){

    require('db_open.php');

    $vendorList = [];

    $sql = <<<strSQL
                SELECT DISTINCT pse.vendor_name, r.loc_id
                FROM repairs r INNER JOIN parts_status pse
            	   ON r.RONum = pse.ro_num AND r.loc_id = pse.loc_id
                WHERE
                    TRIM(r.Estimator) > '' AND
           	        TRIM(pse.vendor_name) NOT LIKE '*%IN%HOUSE%' AND
           	        pse.part_type NOT IN ('Sublet', 'FIX ME') AND
                    pse.part_status IN ('NOT ORDERED', 'ORDERED')
                ORDER BY pse.vendor_name
strSQL;

    try {

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){

            array_push($vendorList, new Vendor($r, $conn));

        }   //while{}

    } catch(Exception $e){
        echo "Fetching Vendor List of parts failed.";
    } finally {

        $conn = null;
        return $vendorList;

    }   // try-catch
}

?>
