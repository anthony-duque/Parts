var app = angular.module("csvUploadApp", []);

var csvUploadCtrlr = function($scope, $filter, $window){

    const currDateTime = new Date();

    $scope.currDateTime = $filter('date')(currDateTime, 'yyyy-MM-dd HH:mm:ss');

    var company_ID = $window.sessionStorage.getItem('companyID');

    if (company_ID > ''){

        $scope.companyID = company_ID;

    } else {

        window.location.href = '../Login.html';

    }   // else (company_ID > '')

    
    $scope.Form_Post_URL = function(){

        return "../../php/admin/Upload_Extracts.php?companyID=" + $scope.companyID;

    }   // $scope.Form_Post_URL()

}

app.controller("csvUploadController", csvUploadCtrlr);
