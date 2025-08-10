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

                SELECT Part_Description, Part_Number, Part_Type,
                    RO_Qty, Part_Status
                FROM PartsStatusExtract
                WHERE TRIM(Vendor_Name) NOT LIKE '*%IN%HOUSE%'
                    AND Part_Type NOT IN ('Sublet', 'FIX ME')
                    AND Loc_ID = $loc_id AND RO_Num = $this->ro_num
                    AND Part_Status IN ('NOT ORDERED', 'ORDERED')
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
                SELECT DISTINCT RO_Num, Vehicle, Owner
                FROM Repairs r INNER JOIN PartsStatusExtract pse
                    ON r.Loc_ID = pse.Loc_ID AND r.RONum = pse.RO_Num
                WHERE r.RONum <> 1004
                    AND TRIM(pse.Vendor_Name) NOT LIKE '*%IN%HOUSE%'
                    AND pse.Part_Type NOT IN ('Sublet', 'FIX ME')
                    AND r.Loc_ID = $loc_id AND r.Estimator = '$this->name'
                    AND pse.Part_Status IN ('NOT ORDERED', 'ORDERED')
                    AND $vendorNameCheck
                ORDER BY RO_Num
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
            FROM Repairs r INNER JOIN PartsStatusExtract pse
                ON r.Loc_ID = pse.Loc_ID AND r.RONum = pse.RO_Num
            WHERE r.RONum <> 1004
                AND TRIM(pse.Vendor_Name) NOT LIKE '*%IN%HOUSE%'
                AND pse.Part_Type NOT IN ('Sublet', 'FIX ME')
                AND r.Loc_ID = $this->locID
                AND pse.Part_Status IN ('NOT ORDERED', 'ORDERED')
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
        $this->name = $rec["Vendor_Name"];
        $this->locID = $rec["Loc_ID"];
        $this->Get_Vendor_Estimators($dbConn);
    }   // __construct()

}   // Vendor{}

//////////////////////////////////////////////////////////

function Get_Parts_By_Vendor_Estimator(){

    require('db_open.php');

    $vendorList = [];

    $sql = <<<strSQL
                SELECT DISTINCT pse.Vendor_Name, r.Loc_ID
                FROM Repairs r INNER JOIN PartsStatusExtract pse
            	   ON r.RONum = pse.RO_Num AND r.Loc_ID = pse.Loc_ID
                WHERE
                    TRIM(r.Estimator) > '' AND
           	        r.RONum <> 1004 AND
           	        TRIM(pse.Vendor_Name) NOT LIKE '*%IN%HOUSE%' AND
           	        pse.Part_Type NOT IN ('Sublet', 'FIX ME') AND
                    pse.Part_Status IN ('NOT ORDERED', 'ORDERED')
                ORDER BY pse.Vendor_Name
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
