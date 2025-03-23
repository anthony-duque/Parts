var sampleApp = angular.module("SampleApp", []);

var sampleCtlr = function($scope, $http){

    GetCars();

    function GetCars(){

        $http.get('./php/sample.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("List of Assigned cars fetched successfully!");
                            console.log(response.data);
                            $scope.cars = response.data;
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Materials list not fetched.");
                    }
             );
    }    // function GetCars()
}   // sampleCtlr

sampleApp.controller("SampleCtlr", sampleCtlr);
