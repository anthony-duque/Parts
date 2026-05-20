<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$carList = Get_All_Cars_By_Tech();
echo json_encode($carList);
exit;

/********************************************/

class TechCars{

    public $technician;
    public $cars = [];

    function __construct($tech){
        $this->technician = $tech;
    }   // TechCars($rec)
}   // TechCars{}


class Car{

    public $ro_num;
    public $owner;
    public $color;
    public $vehicle;
    public $estimator;
    public $technician;

    function __construct($rec){

        $this->ro_num       = $rec["ro_num"];
        $this->owner        = ucwords(strtolower($rec["owner"]));
        $this->vehicle      = $rec["vehicle"];
        $this->estimator    = $rec["estimator"];
        $this->color        = $rec["vehicle_color"];
        $this->technician   = $rec["technician"];

    }   // Car($rec)
}   // Car{}


function Get_All_Cars_By_Tech(){

    require('db_open.php');

    $cars_by_tech = [];

    $sql = <<<strSQL
                SELECT SUBSTRING_INDEX(technician, ' ', 1) AS Technician,
                    ro_num, SUBSTRING_INDEX(owner, ',', 1) AS Owner,
                    vehicle, estimator, vehicle_color
                FROM repairs
                WHERE technician > ''
                ORDER BY technician, vehicle
            strSQL;
    try{

        $s = mysqli_query($conn, $sql);

        $tech = "";

        while($r = mysqli_fetch_assoc($s)){

            if ($r["Technician"] !== $tech){

                if ($tech !== ''){
                    array_push($cars_by_tech, $tech_cars);
                }

                $tech = $r["Technician"];
                $tech_cars = new TechCars($tech);
            }

            array_push($tech_cars->cars, new Car($r));
        }   // while()

        array_push($cars_by_tech, $tech_cars);

    } catch(Exception $e){

        echo "Fetching repairs failed." . $e->getMessage();

    } finally {

        $conn = null;
        return $cars_by_tech;

    }   // try-catch{}

}
?>
