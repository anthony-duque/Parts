var app = angular.module("csvUploadApp", ['ngCookies']);

var csvUploadCtrlr = function($scope, $cookies, $filter){

    const currDateTime = new Date();

    $scope.currDateTime = $filter('date')(currDateTime, 'yyyy-MM-dd HH:mm:ss');

    if ($cookies.get('locationID') > ''){

        var loc_IDs = $cookies.get('locationID').split(',');  // split comma-separated location IDs into array
        console.log("Location IDs: " + loc_IDs);
        $scope.locationID = loc_IDs[0];                       // use first location ID as default (if multiple)

    } else {

        window.location.href = './Login.html';
    
    }   // 

}

app.controller("csvUploadController", csvUploadCtrlr);
