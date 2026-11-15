var stageCtrlr = function($scope, $http, $window, utility){
    
    $scope.locID = $scope.$parent.locationID;  // needed because routing destroys the parent scope's variables
                                                    // so we need to save the location ID in this controller's scope 
                                                    // before routing

    Switch_To_Shop($scope.locID);

    $scope.filterOn         = false;
    $scope.BackgroundMode   = 'Parts Status';

    $scope.priorityCars = [];

////////////////////////////////////

    $scope.$watch('locationID', 

            // Change cars accordingly when shop is switched 
        function(newLocationID, prevLocationID){

            if (newLocationID !== prevLocationID) {
                Switch_To_Shop(newLocationID);
                $scope.estim = "";
                $scope.tech = "";
            }
    }); // $scope.$watch()


    $scope.RemoveFromQueue = function(car){

        var carIndex = -1;

        $scope.priorityCars.forEach((eachCar, i) => {
            if (eachCar.repair_id == car.repair_id){
                carIndex = i;
            }
        });
            // remove the car from the priority queue
        if (carIndex > -1){
            $scope.priorityCars.splice(carIndex, 1);

            $http.delete('./php/Tech_Car_Priority.php?repairID=' + car.repair_id)
                .then(
                    function(response){
                        if(response.data){
                            console.log("Car repair ID $car.repair_id deleted from the Priority Queue.");
                            console.log(response.data);
                        }
                    },
                    function(response){
                        console.log("Car repair ID $car.repair_id was not deleted from the Priority List");
                        console.log(response.status);
                        console.log(response.statusText);
                        console.log(response.headers());
                    }
                );
        }   // if (carIndex...
    }   // RemoveFromQueue()


        // Fetches the stage headings and cars for the selected shop
    function Switch_To_Shop(shop_ID){

        if (shop_ID > 0){
            $http.get('./php/Shop_Stage_Headings.php?shopID=' + shop_ID)  // get all locations by default
                  .then(
                        function(response){
                            if (response.data){
                                console.log("Stages for Shop ID " + shop_ID + " fetched successfully!");
                                console.log(response.data);
                                $scope.stages = response.data;
                                GetCars($scope.stages.length, shop_ID);
                            }
                        }
                  )         // then()
                  .catch(
                        function(response){
                            console.log("Stages list for Shop " + shop_ID + " not fetched.");
                        }
                 );
        }
    }    // function GetStageHeadings()


    function GetPriorityCars(shop_ID){

        $http.get('./php/Tech_Car_Priority.php?shopID=' + shop_ID)  // get all locations by default
              .then(
                    function(response){

                        if (response.data){

                            console.log("Priority Cars fetched successfully!");
                            console.log(response.data);

                            const priorityCars = response.data;

                            var priority_repair_id      = 0;
                            var production_repair_id    = 0;
                            var carFound                = false;

                            priorityCars.forEach((eachCar, i) => {

                                priority_repair_id = eachCar.repair_id;

                                for(var i = 0; i < $scope.production_stage.length; ++i){    // for each stage

                                    carFound    = false;

                                    for(var j = 0; j < $scope.production_stage[i].cars.length; ++j){    // for each car in the stage

                                        production_repair_id      = $scope.production_stage[i].cars[j].repair_id;

                                        if (priority_repair_id == production_repair_id){

                                            $scope.priorityCars.push($scope.production_stage[i].cars[j]);
                                            carFound = true;
                                            break;  // quit searching for the car in the current stage
                                        }
                                    }

                                    if (carFound){
                                        break;    // stop looking for the car in the other stages
                                    }
                                }   // for()
                            });
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Tech Car List not fetched.");
                    }
             );
    }    // GetPriorityCars()


    function GetCars(no_of_stages, shop_ID){

        $http.get('./php/Stage.php?stage_count=' + no_of_stages + '&shopID=' + shop_ID)  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("List of Assigned cars fetched successfully!");
                            console.log(response.data);

                            $scope.production_stage = response.data.stageCars;
                            $scope.lastUpdate = response.data.last_upload_time.toLocaleString();

                            $scope.colWidth         = (1 / $scope.production_stage.length) * 100;

                            GetTechList();      // to populate Tech List dropdown
                            GetEstimatorList(); // to populate Estimator List dropdown

                            GetPriorityCars(shop_ID);  // to populate Priority Cars list

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


    $scope.ChangeBorder = function(selectedCar){

            // Clear border of previous selected car
        for(let i=0; i < $scope.production_stage.length; ++i){
            for(let j=0; j < $scope.production_stage[i].cars.length; ++j){
                $scope.production_stage[i].cars[j].borderColor = '';
            }
        }

        var carFound = false;

        for(let i=0; i < $scope.production_stage.length; ++i){
            
            for(let j=0; j < $scope.production_stage[i].cars.length; ++j){

                if (($scope.production_stage[i].cars[j].repair_id === selectedCar.repair_id)){

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


    function UpdateCarStageInDB(car){

        $http.put('./php/Car_Stage.php', JSON.stringify(car))
            .then(function(response){
                if(response.data){
                    console.log(response.data);
                }
            }, function(response){
                console.log("Car_Stage service does not exist.");
                console.log(response.status);
                console.log(response.statusText);
                console.log(response.headers());
            });
    }   // UpdatePriorityCarInDB()

    $scope.SameAsPickedCar = function(car){

        var sameAsPicked = false;

        if ($scope.carPicked != null){

            if (car.repair_id == $scope.carPicked.repair_id){
                    sameAsPicked = true;
                }
        }

        return sameAsPicked;
    }   // SameAsPickedCar()

    const NOTIFICATION_STAGE = "PAINT";

    function Notify_Estimator(car){

        var url = window.location;
        var pStatLink = url.origin + "/Parts/Car_View.html?roNum=" + car.ro_num + "&locationID=" + car.locationID;
        var partsStatus = "<a href='" + pStatLink + "' target='CarStatus'>" +
                    "HERE</a>";

        var emailSubj = "RO " + car.ro_num + " ( " + car.owner + ") has possible parts issues";

        var emailBody =
                "RO no.  : " + car.ro_num + "<br/>" +
                "Owner   : " + car.owner + "<br/>" +
                "Vehicle : " + car.vehicle + "<br/>" +
                "<br/>" +
                "has been moved to " + NOTIFICATION_STAGE + " stage." + "<br/>" +
                "But there may still be possible issues with its parts." + "<br/>" +
                "Click " + partsStatus + " to check on the vehicle's Parts List";

        var emailTo  = 'ESTIMATOR';
        var emailCC  = 'PARTS_MGR';

        const email = {
            ro_num      :   car.ro_num,
            loc_id      :   car.locationID,
            to          :   emailTo,
            cc          :   emailCC,
            subject     :   emailSubj,
            body        :   emailBody
        };

        $http.post('./php/Email.php', JSON.stringify(email))
            .then(function(response){
                if (response.data.search("successful!") > -1){
                    console.log(response.data);
                    console.log("Estimator Notified");
                } else {
                    console.log("Estimator Notification failed.");
                }
            },
            function(response){
                console.log("Estimator Notification failed");
                console.log(response.status);
                console.log(response.statusText);
                console.log(response.headers());
            });    // $http.post()
//        alert(emailBody);
    }   // Notify_Estimator()


        // Used to move a car to a stage by an increment value
    $scope.MoveStage = function(car, incr){

        // find the RO
        var carFound = false;
        var newStageID = -1;
        var carToMove = null;

        for(let i=0; i < $scope.production_stage.length; ++i){

            if($scope.production_stage[i].cars.length > 0){

                for(let j=0; j < $scope.production_stage[i].cars.length; ++j){

                    if ($scope.production_stage[i].cars[j].repair_id === car.repair_id){

                        carFound = true;

                        newStageID = parseInt($scope.production_stage[i].cars[j].stage_sequence) + incr;

                        $scope.ChangeBorder(car);  // highlight the car

                        $scope.production_stage[i].cars[j].stage_sequence = newStageID;

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
                        UpdateCarStageInDB(carToMove);

                            // if a car was moved to paint with issues with parts
                        if ($scope.stages[newStageID].description.toUpperCase() == NOTIFICATION_STAGE){

                            if(carToMove.parts_percent < 100){
                                Notify_Estimator(carToMove);
                            }   // if (carToMove)
                        }   // if ($scope.stages...)

                            // Remove the car in the Priority Queue if it's there
                        $scope.RemoveFromQueue(carToMove);;
                        $scope.carPicked.borderColor = null;
                        $scope.carPicked = null;
                        break;
                    }   // if (($scope...))
                }   // for (j)
            }   // if()

            if (carFound == true){
                break;                  // don't cyle through the rest of the cars
            }
        }   // for (i)
    }   // MoveStage()


        // Used to move a car by clicking on the stage heading
    $scope.MoveCar = function(stageID){

        if ($scope.carPicked == null){  // no car selected
            ;
        } else {

            var currStageID = $scope.carPicked.stage_sequence;
            var incr = stageID - currStageID;

            if (incr == 0){
                ;
            } else {
                $scope.MoveStage($scope.carPicked, incr);
            }
        }
    }   // MoveCar()


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

        $scope.production_stage.forEach((stage, i) => { // for each stage

            stage.cars.forEach((car, j) => {    // for each car in the stage

                nameFound = false;

                $scope.techList.some((tech) => {    // see if the technician is already in the list
                    if (tech.name == car.technician){
                        nameFound = true;
                    }
                });

                if (nameFound == false){    // if the technician is not in the list
                    if (car.technician.length > 0){
                        technician = new Technician(car.technician, car.locID);
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

        $scope.production_stage.forEach((stage, i) => { // for each stage

            stage.cars.forEach((car, j) => {    // for each car in the stage

                nameFound = false;

                $scope.estimatorList.some((estimator) => {    // see if the estimator is already in the list
                    if (estimator.name == car.estimator){
                        nameFound = true;
                    }
                });

                if (nameFound == false){    // if estimator is not yer in the list
                    if (car.estimator.length > 0){
                        estimator = new Estimator(car.estimator, car.locID);
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

        // Shared by Priority Cars and Stage Cars
    $scope.DoubleClicked = function(carViewPage){
        $window.open('./html/' + carViewPage, "CarStatus");
    }


}   // stageCtrlr()

app.controller("StageCtrlr", stageCtrlr);
