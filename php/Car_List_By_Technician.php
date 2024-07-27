<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$carList = Get_All_Cars_By_Tech();
echo json_encode($carList);
exit;

/********************************************/

class TechCars{

    public $tech;
    public $cars = [];

    function __construct($technician){

        $this->tech = $technician;

    }   // TechCars($rec)
}   // TechCars{}


class Car{

    public $ro_num;
    public $owner;
    public $color;
    public $vehicle;
    public $estimator;

    function __construct($rec){

        $this->ro_num       = $rec["RONum"];
        $this->owner        = ucwords(strtolower($rec["Owner"]));
        $this->vehicle      = $rec["Vehicle"];
        $this->estimator    = $rec["Estimator"];
        $this->color        = $rec["Vehicle_Color"];

    }   // Car($rec)
}   // Car{}


function Get_All_Cars_By_Tech(){

    require('db_open.php');

    $cars_by_tech = [];

    $sql = <<<strSQL
                SELECT SUBSTRING_INDEX(Technician, ' ', 1) AS Technician,
                    RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                    Vehicle, Estimator, Vehicle_Color
                FROM Repairs
                WHERE Technician > ''
                ORDER BY Technician, Vehicle
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
