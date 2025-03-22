<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const CHECKINPRESCAN    = 0;
const DISSEMBLY         = 1;
const REPAIR_PLAN       = 2;
const PENDING_APPROVAL  = 3;
const WAITINGFORPARTS   = 4;
const BODY_WORK         = 5;
const FOR_PRIMER        = 6;
const FOR_PAINT         = 7;
const DELAYED           = 8;
const REASSEMBLY        = 9;
const SUBLET            = 10;
const FOR_DETAIL        = 11;
const FINALQC_POSTSCAN  = 12;
const READYFORDELIVERY  = 13;

$productionStages = [];

for($stageID = CHECKINPRESCAN; $stageID <= READYFORDELIVERY; ++$stageID){
    $stageCars = new StageCars($stageID);
//    echo json_encode($stageCars->cars);
    $productionStages[$stageID] = $stageCars->cars;
}

echo json_encode($productionStages);


class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $color;
    public $estimator;
    public $locationID;
    public $currentPhase;
    public $stageID;

    function __construct($rec){
        $this->ro_num   = $rec["RONum"];
        $this->owner    = $rec["Owner"];
        $this->vehicle  = $rec["Vehicle"];
        $this->color    = $rec["Vehicle_Color"];
        $this->technician = $rec["Technician"];
        $this->estimator  = $rec["Estimator"];
        $this->locationID = $rec["Loc_ID"];
        $this->currentPhase = $rec["CurrentPhase"];
        $this->stageID    = $rec["Stage_ID"];
    }   // constructor()
}  // class Car{}


class StageCars{

    public $cars = [];

    function GetCars($stage_ID){

        $strSQL = <<<sqlStmt
                    SELECT RONum, Owner, Vehicle, Vehicle_In, CurrentPhase,
                            Technician, Estimator, Vehicle_Color, Loc_ID,
                            Stage_ID
                    FROM Repairs
                    WHERE Stage_ID = $stage_ID
                sqlStmt;

        require('db_open.php');

        $s = mysqli_query($conn, $strSQL);

        while($r = mysqli_fetch_assoc($s)){
            array_push($this->cars, new Car($r));
        }   // while()

        $conn = null;

        return $this->cars;
    }   // function()

    function __construct($sID){
        $this->cars = $this->GetCars($sID);
    }   // function __construct()

}   // ProductionStage{}

?>
