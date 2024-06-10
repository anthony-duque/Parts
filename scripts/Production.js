
var prodController = function($scope, utility){

    $scope.carsInOut    = 'all';
    $scope.sortCars     = '+parts_percent';
    $scope.sortMode     = 'Sort by Parts Received';

        // default View is Technician View
    $scope.productionView = 'Technician_View.html';

    $scope.SortCars = function (sortField){

        $scope.sortCars = utility.SortField(sortField, $scope.sortCars);

        var plusMinus = $scope.sortCars.substring(0,1);

        if (plusMinus == '-'){
            $scope.buttonClass = "noParts";
            $scope.sortMode = "Sort by Parts Incomplete";
        } else {
            $scope.buttonClass = "partsComplete";
            $scope.sortMode = "Sort by Parts Complete";
        }
    }

    $scope.SortCars($scope.sortCars);   //initialize the screen

}   // prodController()

app.controller("prodController", prodController);
