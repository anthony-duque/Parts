//var app = angular.module("UnorderedPartsApp", []);

function UnorderedPartsCtrlr($scope, $http, utility){

    GetPartsList();
    $scope.carsInOut = 'all';
    $scope.expColl = '+';

    function GetPartsList()
    {
        $http.get('./php/Unordered_Parts.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetUnorderedParts()


    function handleSuccess(response)
    {
        if (response.data){
         console.log("Unordered parts fetched successfully!");
         //console.log(response.data);
         $scope.estimators = response.data;
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Unordered Parts list not fetched.");
    }   // handleError()


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


    $scope.ShowHideParts = function(estimator){

        var showParts = false;

        estimator.cars.forEach((car) => {
            if (car.showParts == true){
                showParts = true;
            }
        });

        estimator.cars.forEach((car) => {
            car.showParts = !showParts
        });

    }   // ShowHideParts()


    $scope.ExpandCollapse = function(){

        $scope.estimators.forEach((estimator) => {
            $scope.ShowHideParts(estimator);
        });

        if ($scope.expColl == '+'){
            $scope.expColl = '-';
        } else {
            $scope.expColl = '+';
        }
    }

}   // UnorderedPartsCtrlr()

app.controller("UnorderedPartsController", UnorderedPartsCtrlr);
