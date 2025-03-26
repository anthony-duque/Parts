var stageApp = angular.module("StageApp", []);

var stageCtrlr = function($scope, $http, $window){

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


    $scope.DoubleClicked = function(carViewPage){
        // alert("life sucks");
        $window.open(carViewPage, "CarStatus");
    }


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
            }   // for (let j)

            if (carFound == true){
                break;
            }
        }   // for (let i)
    }   // ChangeBorder()


    $scope.MoveStage = function(roNum, locID, incr){

        // find the RO
        var carFound = false;
        var newStageID = -1;

        for(let i=0; i < $scope.cars.length; ++i){

            if($scope.cars[i].length > 0){

                for(let j=0; j < $scope.cars[i].length; ++j){

                    if (($scope.cars[i][j].ro_num === roNum) &&
                        ($scope.cars[i][j].locationID === locID)){

                        carFound = true;
                        newStageID = parseInt($scope.cars[i][j].stageID) + incr;

                        $scope.ChangeBorder(roNum, locID);
                        $scope.cars[i][j].stageID = newStageID;
                        $scope.cars[i + incr].push($scope.cars[i][j]);
                        $scope.cars[i].splice(j, 1);

                        carObj = {
                            "ro_Num"    : roNum,
                            "loc_ID"    : locID,
                            "stage_ID"  : newStageID
                        };

                            // update record in db
                        $http.put('./php/Stage.php', JSON.stringify(carObj))
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
                break;
            }
        }   // for (i)
    }   // MoveStage()

}   // sampleCtlr

stageApp.controller("StageCtrlr", stageCtrlr);
