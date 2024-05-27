//var app = angular.module("UnorderedPartsApp", []);

function UnorderedPartsCtrlr($scope, $http, utility){

    GetPartsList();
    $scope.carsInOut = 'all';

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
         console.log(response.data);
         $scope.estimators = response.data;
        }
    }   // handleSuccess()

    function handleError(response)
    {
        console.log("Unordered Parts list not fetched.");
    }   // handleError()

    $scope.ToggleRow = function(x){
        return utility.ToggleRows(x);
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

}   // UnorderedPartsCtrlr()

app.controller("UnorderedPartsController", UnorderedPartsCtrlr);

app.filter('FilterInOutCars', function(){

    return function(cars, inOut){

        var filteredCars = [];
        var date_in = null;
        var curr_date = new Date();

        switch(inOut){

            case 'inShop':

                angular.forEach(cars, function(car){
                    if(car.vehicle_in > ''){
                        date_in = Date.parse(car.vehicle_in);
                        if (date_in <= curr_date){
                            filteredCars.push(car);
                        }
                    }
                });
                break;

            case 'preOrder':

                angular.forEach(cars, function(car){

                    if(car.vehicle_in.length == 0){
                        filteredCars.push(car);
                    }else{
                        date_in = Date.parse(car.vehicle_in);
                        if (date_in > curr_date){
                            filteredCars.push(car);
                        }
                    }
                });
                break;

            default:
                filteredCars = cars;
                break;
        }

        return filteredCars;
    }   // function(cars, inOut)

});
