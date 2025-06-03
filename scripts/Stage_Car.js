var StageCarCtrlr = function($scope, $http, $window, utility){

        // Used to move a car to a stage by an increment value
    $scope.MoveStage = function(car, incr){

        // find the RO
        var carFound = false;
        var newStageID = -1;
        var carToMove = null;

        for(let i=0; i < $scope.production_stage.length; ++i){

            if($scope.production_stage[i].cars.length > 0){

                for(let j=0; j < $scope.production_stage[i].cars.length; ++j){

                    if (($scope.production_stage[i].cars[j].ro_num === car.ro_num) &&
                        ($scope.production_stage[i].cars[j].locationID === car.locationID)){

                        carFound = true;

                        newStageID = parseInt($scope.production_stage[i].cars[j].stageID) + incr;

                        $scope.ChangeBorder(car);  // highlight the car

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
                                console.log("Car_Stage service does not exist.");
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

    $scope.PlaceInQueue = function(car){

        var foundCarInQueue = false;

            // check if the car is already in the priorityList
        $scope.priorityCars.forEach((eachCar, i) => {
            if (eachCar.ro_num == car.ro_num){
                foundCarInQueue = true;
            }
        });

        if(foundCarInQueue){
            alert("Car is already in the queue.");
            return;     // car already in queue.  Just return.
        }

        // place the car in queue if not already there

        $scope.priorityCars.push(car);

            // insert car in the database
        $http.post('./php/Tech_Car_Priority.php', JSON.stringify(car))

            .then(function(response){
                if (response.data){
                    console.log(response.data);
                    console.log("Car added to Priority List");
                }
            },
            function(response){
                console.log("Car was not added to Priority List");
                console.log(response.status);
                console.log(response.statusText);
                console.log(response.headers());
            });

        }
       // PlaceInQueue()

}   // StageCarCtrlr()

app.controller("StageCarController", StageCarCtrlr);
