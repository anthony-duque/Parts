<?php

    $groupBy = $_GET["groupBy"];

    $deliveries = ProcessGET($groupBy);
    echo json_encode($deliveries);

/**             **/
    class Part{
        public $number;
        public $description;
        public $vendor_name;
        public $received_qty;
        public $invoice_date;
    }


    class Parts_By_Car{
        public $ro_num;
        public $owner;
        public $vehicle;
        public $technician;
        public $estimator;
        public $parts = [];
    }


    class Vendor_Cars{
        public $vendor_name;
        public $cars = [];
    }


    class Vendors_By_Estimator {
        public $estimator;
        public $vendors = [];
    }


    class Cars_By_Vendor {
        public $vendor;
        public $cars = [];
    }


    class Vendors_By_Car {
        public $car;
        public $vendors = [];
    } // ORDER BY ROnum, Vendor


    function ProcessGET()
    {
        $sql = <<<strSQL
                    SELECT r.RONum, r.Vehicle, r.Estimator, r.Technician, r.Owner,
                            pse.Part_Number, pse.Part_Description, pse.Vendor_Name,
                            pse.Received_Qty, pse.Invoice_Date
                    FROM PartsStatusExtract pse INNER JOIN Repairs r
                        ON pse.RO_Num = r.RONum
                    WHERE Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                strSQL;

        try{

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = new Part($r);
                array_push($parts, $part);

            }   // while()

        } catch(Exception $e){

            echo "Fetching RO details failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $car;

        }   // try-catch{}

    }       // ProcessGET()



?>
