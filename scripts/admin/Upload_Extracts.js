var app = angular.module("csvUploadApp", []);

var csvUploadCtrlr = function($scope, $filter, $window){

    const currDateTime = new Date();

    $scope.currDateTime = $filter('date')(currDateTime, 'yyyy-MM-dd HH:mm:ss');
    $scope.companyID = $window.sessionStorage.getItem('companyID');

    var loc_IDs = $window.sessionStorage.getItem('locationIDs');
    
    if (loc_IDs > ''){

        console.log("Location IDs: " + loc_IDs);
        $scope.locationID = loc_IDs[0];                       // use first location ID as default (if multiple)

    } else {

        window.location.href = '../Login.html';
    
    }   //
    
    $scope.Form_Post_URL = function(){

        return "../../php/admin/Upload_Extracts.php?locationID=" + $scope.locationID;

    }   // $scope.Form_Post_URL()

}

app.controller("csvUploadController", csvUploadCtrlr);
