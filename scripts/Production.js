
var app = angular.module("prodModule", []);

var prodController = function($scope){

    $scope.productionView = 'Car_View.html';
}   // prodController()

app.controller("prodController", prodController);
