var app = angular.module("PartsApp", ['ngRoute']);

var mainController = function($scope, $http, $window, utility){

    $scope.todaysDate = new Date().toLocaleDateString();
    $scope.locationID = '';

    $scope.locationIDs = $window.sessionStorage.getItem('locationIDs');
    $scope.companyID = $window.sessionStorage.getItem('companyID');

    if ($scope.companyID === null || $scope.companyID === undefined){

        $window.location.href = './html/Login.html';    

    } else {    // company is logged in, but check if they have locationIDs stored in session storage

        if ($scope.locationIDs > ''){

            var loc_IDs = $scope.locationIDs.split(',');  // split comma-separated location IDs into array
            $scope.locationID = loc_IDs[0];                       // use first location ID as default (if multiple)

            Get_Shop_Locations();
        
        } else {

            alert("No shops are associated with this account. Please upload CSV extracts.");
            $window.location.href = './html/admin/Admin.html';

        }   // if($scope.locationIDs > '') ... else ...

    }   // if ($scope.companyID === null || $scope.companyID === undefined) ... else ...


    // To default to a specific shop via the query string, use: index.html?locationID=1 (or 2, 3, etc.)
 /*
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const locID = urlParams.get('locationID');
*/
    /////////////////////////////////////////////

    function Get_Shop_Locations(){
        $http.get('./php/index.php')
            .then(handleSuccess)
            .catch(handleError);
    }   // Get_Shop_Locations()


    function handleSuccess(response)
    {
        if (response.data){
            console.log(response.data);

            $scope.locations = response.data.locations;
            $scope.last_update = response.data.last_upload_time.toLocaleString();
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Shop locations not fetched.");
    }


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


    $scope.Logout = function(){

        $window.sessionStorage.removeItem('companyID');
        $window.sessionStorage.removeItem('locationIDs');

        $window.location.href = './html/Login.html';

    }   // Logout()

}

app.controller("MainController", mainController);
