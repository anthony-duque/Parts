var app = angular.module("AdminPage");

var adminController = function($scope, $window){

    $scope.Logout = function(){

        $window.sessionStorage.removeItem('companyID');
        $window.sessionStorage.removeItem('locationIDs');

        $window.location.href = './html/Login.html';

    }   // Logout()

}   // adminController()

app.controller("AdminController", adminController);