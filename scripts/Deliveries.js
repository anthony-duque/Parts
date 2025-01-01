
var DeliveriesCtrlr = function($scope, utility){

    $scope.carsInOut = "all";
    $scope.viewBy = "DeliveriesByCar.html";
    $scope.numDays = 365;

    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()
}

app.controller("DeliveriesController", DeliveriesCtrlr);
