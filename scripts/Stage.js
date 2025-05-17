var app = angular.module("StageApp", []);

var stageCtrlr = function($scope, $http, $window, utility){

    $scope.filterOn         = false;
    $scope.BackgroundMode   = 'Parts Status';

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const locID = urlParams.get('locationID');

    if (locID > ''){    // Get the cars automatically
                        //  if the location is set in querystring
        $scope.locID = locID;
        GetStageHeadings(locID);
        GetUploadTimeStamp();
    }

////////////////////////////////////

    function GetUploadTimeStamp(){

        $http.get('./php/Get_Upload_Time.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("Last Upload Time fetched successfully!");
                            console.log(response.data);

                            const last_update = new Date (response.data);
                            console.log("converted time: " + last_update);
                            $scope.lastUpdate = last_update.toLocaleString();
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Last Upload Time not fetched.");
                    }
             );
    }    // GetUploadTimeStamp()


    function GetStageHeadings(loc_ID){

        if (loc_ID > 0){
            $http.get('./php/Stage_Headings.php?locationID=' + $scope.locID)  // get all locations by default
                  .then(
                        function(response){
                            if (response.data){
                                console.log("Stages fetched successfully!");
                                console.log(response.data);
                                $scope.stages = response.data;
                                GetCars($scope.stages.length, loc_ID);
                            }
                        }
                  )         // then()
                  .catch(
                        function(response){
                            console.log("Stages list not fetched.");
                        }
                 );
        }
    }    // function GetStageHeadings()


    function GetCars(num_of_stages, locationID){

        $http.get('./php/Stage.php?stages_count=' + num_of_stages + '&locID=' + locationID)  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("List of Assigned cars fetched successfully!");
                            console.log(response.data);

                            $scope.production_stage = response.data.stageCars;

                            $scope.colWidth         = (1 / $scope.production_stage.length) * 100;

                            GetTechList();      // to populate Tech List dropdown
                            GetEstimatorList(); // to populate Estimator List dropdown

                            SetProperShop();

                        }   // if (response.data)
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Production cars not fetched.");
                    }
             );
    }    // function GetCars()


    $scope.SwitchShops = function(locationID){
        GetStageHeadings(locationID);
    }


    $scope.DoubleClicked = function(carViewPage){
        $window.open(carViewPage, "CarStatus");
    }


    $scope.ChangeBorder = function(roNum, locID){

        for(let i=0; i < $scope.production_stage.length; ++i){
            for(let j=0; j < $scope.production_stage[i].cars.length; ++j){
                $scope.production_stage[i].cars[j].borderColor = '';
            }
        }

        var carFound = false;
        for(let i=0; i < $scope.production_stage.length; ++i){
            for(let j=0; j < $scope.production_stage[i].cars.length; ++j){

                if (($scope.production_stage[i].cars[j].ro_num === roNum) &&
                    ($scope.production_stage[i].cars[j].locationID === locID)){

                    $scope.production_stage[i].cars[j].borderColor = 'red';

                    carFound = true;
                    $scope.carPicked = $scope.production_stage[i].cars[j];

                    break;
                }
            }   // for (let j)

            if (carFound == true){
                break;
            }
        }   // for (let i)
    }   // ChangeBorder()


        // Used to move a car by clicking on the stage heading
    $scope.MoveCar = function(stageID){

        if ($scope.carPicked == null){  // no car selected
            ;
        } else {
            var currStageID = $scope.carPicked.stageID;
            var incr = stageID - currStageID;
            $scope.MoveStage($scope.carPicked.ro_num, $scope.carPicked.locationID, incr);
        }
    }   // MoveCar()


        // Used to move a car to a stage by an increment value
    $scope.MoveStage = function(roNum, locID, incr){

        // find the RO
        var carFound = false;
        var newStageID = -1;
        var carToMove = null;

        for(let i=0; i < $scope.production_stage.length; ++i){

            if($scope.production_stage[i].cars.length > 0){

                for(let j=0; j < $scope.production_stage[i].cars.length; ++j){

                    if (($scope.production_stage[i].cars[j].ro_num === roNum) &&
                        ($scope.production_stage[i].cars[j].locationID === locID)){

                        carFound = true;

                        newStageID = parseInt($scope.production_stage[i].cars[j].stageID) + incr;

                        $scope.ChangeBorder(roNum, locID);  // highlight the car

                        $scope.production_stage[i].cars[j].stageID = newStageID;

                            // insert the car to its new stage
                        carToMove = $scope.production_stage[i].cars[j];

                            // move the car to the bottom or insert somewhere you took it from
                        if (j >= $scope.production_stage[i + incr].cars.length){
                            $scope.production_stage[i + incr].cars.push(carToMove);
                        } else {
                            $scope.production_stage[i + incr].cars.splice(j, 0, carToMove);
                        }

                            // remove the car from it's previous stage
                        $scope.production_stage[i].cars.splice(j, 1);

                            // update record in db
                        $http.put('./php/Car_Stage.php', JSON.stringify(carToMove))
                            .then(function(response){
                                if(response.data){
                                    console.log(response.data);
                                }
                            }, function(response){
                                console.log("Service does not exist.");
                                console.log(response.status);
                                console.log(response.statusText);
                                console.log(response.headers());
                            });
                        break;
                    }   // if (($scope...))
                }   // for (j)
            }   // if()

            if (carFound == true){
                break;                  // don't cyle through the rest of the cars
            }
        }   // for (i)
    }   // MoveStage()


    $scope.ChooseBGMode = function(bgm){

        if (bgm == 'Insurance'){
            $scope.BackgroundMode = 'Parts Status';
        } else {
            $scope.BackgroundMode = 'Insurance';
        }
    }


    $scope.Set_BG_Class = function(bgMode, car){

        var bgClass = '';

        if (bgMode == 'Insurance'){
            bgClass = utility.ColorCarInsurance(car.insurance);
        } else {
            bgClass = utility.ColorCarPartsStatus(car);
        }

        return bgClass;
    }


    function GetTechList(){

        function Technician(name, loc_ID) {
          this.name = name;
          this.locationID = loc_ID;
        }   //

        $scope.techList = [];

        var technician  = null;
        var nameFound   = false;

        $scope.production_stage.forEach((stage, i) => {
            stage.cars.forEach((car, j) => {

                nameFound = false;

                $scope.techList.forEach((technician, i) => {
                    if (technician.name == car.technician){
                        nameFound = true;
                    }
                });

                if (nameFound == false){    // if estimator is not yer in the list
                    if (car.technician.length > 0){
                        technician = new Technician(car.technician, car.locationID);
                        $scope.techList.push(technician);       // add him
                    }
                }

            });
        });
        console.log($scope.techList);
    }   // GetTechList()


    function GetEstimatorList(){

        function Estimator(name, loc_ID) {
          this.name = name;
          this.locationID = loc_ID;
        }   //

        $scope.estimatorList    = [];

        var estimator   = null;
        var nameFound   = false;

        $scope.production_stage.forEach((stage, i) => {
            stage.cars.forEach((car, j) => {

                nameFound = false;

                $scope.estimatorList.forEach((estimator, i) => {
                    if (estimator.name == car.estimator){
                        nameFound = true;
                    }
                });

                if (nameFound == false){    // if estimator is not yer in the list
                    if (car.estimator.length > 0){
                        estimator = new Estimator(car.estimator, car.locationID);
                        $scope.estimatorList.push(estimator);       // add him
                    }
                }
            });
        });
        console.log($scope.estimatorList);
    }   // GetTechList()


    $scope.ResetFilters = function(){

        $scope.tech = "";
        $scope.estim = "";
        $scope.searchText = "";
        $scope.filterOn = false;    // Unassigned Tech filter off
        $scope.UC_Class = '';

    }   // ResetFilters()


    $scope.UnassignedCarsFilter = function(){

        $scope.tech = "";
        $scope.estim = "";
        $scope.searchText = "";

        $scope.filterOn = !$scope.filterOn;

        if ($scope.filterOn){
            $scope.UC_Class = 'greenOnWhite';
        } else {
            $scope.UC_Class = '';
        }

    }   // UnassignedCarsFilter()


    $scope.BG_SubletsStatus = function(subletList){

        var bgClass = "waitingForParts";

        if ((subletList == null) || (subletList.length == 0)){

            bgClass = "lightGreen";

        } else {

            var count_done = 0;

            subletList.forEach((sublet, i) => {
                if(sublet.received_quantity > 0){
                    ++count_done;
                }
            });

            switch (true) {

                case (count_done == 0):
                    bgClass = "whiteOnRed";
                    break;

                case (count_done == subletList.length):
                    bgClass = "lightGreen";
                    break;

                default:
                    break;
            }
        }

        return bgClass;
    }   // BG_SubletsStatus()

}   // stageCtrlr()

app.controller("StageCtrlr", stageCtrlr);
