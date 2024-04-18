var sample = angular.module("SampleApp", []);

var sampleController = function($scope){

    $scope.paintList = [];

    $scope.techList = ["Highlander", "Pathfinder", "Pilot"];

    $scope.AddToPaintList = function(car){

        $scope.paintList.push(car);

        var carIndex = $scope.techList.indexOf(car);
        $scope.techList.splice(carIndex, 1);

    }   // AddToPaintList()

    $scope.DeleteFromPaintList = function(car){

        $scope.techList.push(car);

        var carIndex = $scope.paintList.indexOf(car);
        $scope.paintList.splice(carIndex, 1);

    }   // DeleteFromPaintList()

}

sample.controller("SampleCtrlr", sampleController)
