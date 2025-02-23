
var prodController = function($scope, utility){

    $scope.carsInOut    = 'all';
    $scope.sortCars     = '+parts_percent';
    $scope.sortMode     = 'Sort by Parts Received';
    $scope.dueMode      = "What's not Due";

        // default View is Technician View
    $scope.productionView = 'Technician_View.html';

    $scope.SortCarsDue = function (sortField){

        $scope.sortCars = utility.SortField(sortField, $scope.sortCars);

        var plusMinus = $scope.sortCars.substring(0,1);

        if (plusMinus == '-'){
            $scope.DueButtonClass = "noParts";
            $scope.dueMode = "By what's not due";
        } else {
            $scope.DueButtonClass = "partsComplete";
            $scope.dueMode = "By what's Due";
        }
    }

    $scope.SortCars = function (sortField){

        $scope.sortCars = utility.SortField(sortField, $scope.sortCars);

        var plusMinus = $scope.sortCars.substring(0,1);

        if (plusMinus == '-'){
            $scope.buttonClass = "noParts";
            $scope.sortMode = "What needs Parts";
        } else {
            $scope.buttonClass = "partsComplete";
            $scope.sortMode = "What's Ready";
        }
    }

    $scope.SortCars($scope.sortCars);   //initialize the screen

}   // prodController()

app.controller("prodController", prodController);
