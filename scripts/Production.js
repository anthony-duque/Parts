
var prodController = function($scope){

    $scope.carsInOut = 'all';
        // default View is Technician View
    $scope.productionView = 'Technician_View.html';

}   // prodController()

app.controller("prodController", prodController);
