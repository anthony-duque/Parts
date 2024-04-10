var carController = function($scope, $http){

    $scope.sample = "sample";

    $scope.car = {
        "ro_num"    : 2594,
        "owner"     : "Mansfield",
        "vehicle"   : "2008 Toyota Tacoma",
        "technician": "Jerry Saucedo",
        "estimator" : "Sep Sadhegi"
    };

}

app.controller("carController", carController);
