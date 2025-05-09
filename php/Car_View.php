<?php

require('Utility_Scripts.php');

    $ro_num = $_GET["roNum"];
    $locationID = $_GET["locationID"];

    $CarParts = ProcessGET($ro_num, $locationID);

    echo json_encode($CarParts);

    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $license_plate;
        public $vehicle_in;
        public $scheduled_out;
        public $estimator;
        public $technician;
        public $location;
        public $insurance;
        public $partsList   = [];
        public $subletList  = [];

        function __construct($rec){

            $this->ro_num           = $rec["RONum"];
            $this->owner            = $rec["Owner"];
            $this->vehicle          = $rec["Vehicle"];
            $this->vehicle_color    = toProperCase($rec["Vehicle_Color"]);
            $this->license_plate    = $rec["License_Plate"];
            $this->vehicle_in       = GetDisplayDate($rec["Vehicle_In"]);
            $this->scheduled_out    = GetDisplayDate($rec["Scheduled_Out"]);
            $this->estimator        = $rec["Estimator"];
            $this->technician       = toProperCase($rec["Technician"]);
            $this->location         = $rec["Location"];
            $this->insurance        = $rec["Insurance"];
        }   // Car()
    }   // Car{}

    class Part{

        public $line_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $order_date;
        public $received_quantity;
        public $invoice_date;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;
        public $part_status;

        function __construct($rec){

            $this->line_num             = $rec["Line"];
            $this->part_number          = $rec["Part_Number"];
            $this->part_description     = $rec["Part_Description"];

            $this->vendor_name          = strtolower($rec["Vendor_Name"]);
            $this->vendor_name          =  ucwords($this->vendor_name);

            $this->ro_quantity          = $rec["RO_Qty"];
            $this->ordered_quantity     = $rec["Ordered_Qty"];
            $this->order_date           = GetDisplayDate($rec["Order_Date"]);
            $this->received_quantity    = $rec["Received_Qty"];
            $this->invoice_date         = GetDisplayDate($rec["Invoice_Date"]);
            $this->returned_quantity    = $rec["Returned_Qty"];
            $this->expected_delivery    = GetDisplayDate($rec["Expected_Delivery"]);

            $this->part_status          = ComputePartStatus(
                                            $this->ro_quantity,
                                            $this->ordered_quantity,
                                            $this->received_quantity,
                                            $this->returned_quantity
                                        );
        }   // Part()
    }   // Part{}


    class Sublet{

        public $part_description;
        public $vendor_name;
        public $received_quantity;

        function __construct($rec){
            $this->part_description     = $rec["Part_Description"];
            $this->vendor_name          = $rec["Vendor_Name"];
            $this->received_quantity    = $rec["Received_Qty"];
        }
    }   // Sublet{}


    function GetPartsList($ro, $locID, $dbConn){

        $sql = <<<strSQL
                SELECT Line, Part_Number, Part_Description, Order_Date,
                        Vendor_Name, RO_Qty, Ordered_Qty, Received_Qty,
                        Returned_Qty, Expected_Delivery, Invoice_Date
                FROM PartsStatusExtract
                WHERE (Part_Number <> 'Remanufactured')
                    AND (Line > 0)
                    AND (Part_Number > '' OR Vendor_Name > '')
                    AND Vendor_Name NOT LIKE '**%'
                    AND Part_Number NOT LIKE 'Aftermarket%'
                    AND (Part_Type <> 'Sublet')
                    AND RO_Num = $ro
                    AND Loc_ID = $locID
                ORDER BY Ordered_Qty ASC;
            strSQL;
        try{

            $parts = [];

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = new Part($r);
                array_push($parts, $part);

            }   // while()

            return $parts;

        } catch(Exception $e){

            echo "Fetching RO parts failed." . $e->getMessage();

        }   // try-catch{}
    }   // GetPartsList()


    function GetSubletList($ro_num, $loc_id, $dbConn){

        $sublets = [];

        $sql =  <<<strSQL
                    SELECT Part_Description, Vendor_Name,
                        Received_Qty
                    FROM PartsStatusExtract
                    WHERE Part_Type = 'Sublet'
                        AND RO_Num = $ro_num
                        AND Loc_ID = $loc_id
                    ORDER BY Received_Qty
                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($sublets, new Sublet($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Sublet List failed.";
        }   // try-catch

        return $sublets;
    }   // Get_Sublet_List()


    function ProcessGET($roNum, $locID){

        require('db_open.php');

        $sql = <<<strSQL
                    SELECT RONum, Owner, Vehicle, Estimator, Technician,
                        Vehicle_Color, License_Plate, Vehicle_In, Scheduled_Out,
                        Location, Loc_ID, Insurance
                    FROM Repairs
                    WHERE RONum = $roNum AND Loc_ID = $locID
                strSQL;

        try{

            $s = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($s);

            $car = new Car($r);
            $car->partsList = GetPartsList($roNum, $locID, $conn);
            $car->subletList = GetSubletList($roNum, $locID, $conn);

        } catch(Exception $e){

            echo "Fetching RO details failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $car;

        }   // try-catch{}
    }   // ProcessGET($roNum)

?>
