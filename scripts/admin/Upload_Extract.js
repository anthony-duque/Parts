var app = angular.module("csvUploadApp", []);

var csvUploadCtrlr = function($scope, $window, $filter, $http) {

    const currDateTime = new Date();

    $scope.currDateTime = $filter('date')(currDateTime, 'yyyy-MM-dd HH:mm:ss');

    $scope.locationIDs = $window.sessionStorage.getItem('locationIDs');
    $scope.companyID = $window.sessionStorage.getItem('companyID');

    if ($scope.companyID > ''){

        console.log("Company ID: " + $scope.companyID);

        if ($scope.locationIDs > ''){
            console.log("Location IDs: " + $scope.loc_IDs);
            $scope.loc_IDs = $scope.locationIDs.split(',');  // split comma-separated location IDs into array
        }
        
    } else {

        window.location.href = '../Login.html';
    
    }   // if ($scope.locationIDs > '')

    
    $scope.formURL = function() {

        return "../../php/admin/Upload_Extract.php?companyID=" + $scope.companyID;
    }

}   // csvUploadCtrlr()

app.controller("csvUploadController", csvUploadCtrlr);
