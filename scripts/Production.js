
var app = angular.module("prodModule", []);

var prodController = function($scope, $rootScope){

    $rootScope.productionView = 'Technician_View.html';
}   // prodController()

app.controller("prodController", prodController);
