var app = angular.module("OrderMaterialsApp", []);

var OrderMaterialsCtrlr = function($scope, $http){
    $scope.x = "ohhh";
};

app.controller("OrderMaterialsController", OrderMaterialsCtrlr);
