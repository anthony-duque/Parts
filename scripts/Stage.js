var stageApp = angular.module("StageApp", []);

var stageCtrlr = function($scope, $http){

    GetCars();

    function GetCars(){

        $http.get('./php/Stage.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("List of Assigned cars fetched successfully!");
                            console.log(response.data);
                            $scope.cars = response.data;
                            $scope.colWidth = (1 / $scope.cars.length) * 100;
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Materials list not fetched.");
                    }
             );
    }    // function GetCars()

    $scope.ChangeBorder = function(roNum, locID){

        for(let i=0; i < $scope.cars.length; ++i){
            for(let j=0; j < $scope.cars[i].length; ++j){
                $scope.cars[i][j].borderColor = '';
            }
        }

        var carFound = false;
        for(let i=0; i < $scope.cars.length; ++i){
            for(let j=0; j < $scope.cars[i].length; ++j){

                if (($scope.cars[i][j].ro_num === roNum) &&
                    ($scope.cars[i][j].locationID === locID)){
                    $scope.cars[i][j].borderColor = 'red';
                    carFound = true;
                    break;
                }
                if (carFound == true){
                    break;
                }
            }
        }

    }

    $scope.MoveStage = function(roNum, locID, incr){

        // find the RO
        var carFound = false;
        for(let i=0; i < $scope.cars.length; ++i){

            if($scope.cars[i].length > 0){
                for(let j=0; j < $scope.cars[i].length; ++j){

                    if (($scope.cars[i][j].ro_num === roNum) &&
                        ($scope.cars[i][j].locationID === locID)){

                            carFound = true;
                            $scope.cars[i][j].stageID += incr;
                            $scope.cars[i + incr].push($scope.cars[i][j]);
                            $scope.cars[i].splice(j, 1);
                            break;
                            // update record in db
                        }   // if (($scope...))

                }   // for (j)
            }   // if()

            if (carFound == true){
                break;
            }
        }   // for (i)
    }

}   // sampleCtlr

stageApp.controller("StageCtrlr", stageCtrlr);
