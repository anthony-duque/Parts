var app = angular.module("csvUploadApp", []);

var csvUploadCtrlr = function($scope){

    const currDateTime = new Date();

    $scope.currDateTime = currDateTime.toString();

}

app.controller("csvUploadController", csvUploadCtrlr);
