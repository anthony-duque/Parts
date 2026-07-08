var app = angular.module("PartsApp", ['ngRoute']);

var mainController = function($scope, $http, $window, utility){

    $scope.todaysDate = new Date().toLocaleDateString();
    $scope.locationID = '';

    $scope.companyID = $window.sessionStorage.getItem('companyID');

    if ($scope.companyID === null || $scope.companyID === undefined){

        $window.location.href = './html/Login.html';    

    } else {    // company is logged in, but check if they have locationIDs stored in session storage

        Get_Shop_Locations();

    }   // if ($scope.companyID === null || $scope.companyID === undefined) ... else ...


    /////////////////////////////////////////////

    function Get_Shop_Locations(){

        $http.get('./php/index.php?companyID=' + $scope.companyID)
            .then(handleSuccess)
            .catch(handleError);

    }   // Get_Shop_Locations()


    function handleSuccess(response)
    {
        if (response.data){

            console.log(response.data);

            $scope.locations = response.data.locations;

            if ($scope.locations.length > 0){

                $scope.locationID = $scope.locations[0].id;  // use first shop as default (if there are multiple)
            
            } else {

                alert("No shops are associated with this account. Please upload CSV extracts.");
                $window.location.href = './html/admin/Admin.html';

            }   // if($scope.locationIDs > '') ... else ...

            $scope.UpdateUploadDate();
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Shop locations not fetched.");
    }


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


    $scope.UpdateUploadDate = function(){

        let location = $scope.locations.find(loc => loc.id === $scope.locationID);
        $scope.last_update = location.last_data_upload.toLocaleString();
        
    }   // UpdateUploadDate()


    $scope.Logout = function(){

        $window.sessionStorage.removeItem('companyID');
        $window.location.href = './html/Login.html';

    }   // Logout()

}

app.controller("MainController", mainController);
