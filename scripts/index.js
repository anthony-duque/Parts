var app = angular.module("PartsApp", ['ngCookies', 'ngRoute']);

var mainController = function($scope, $http, $cookies, utility){

    $scope.locationID = '';

    if ($cookies.get('locationID') > ''){

        var loc_IDs = $cookies.get('locationID').split(',');  // split comma-separated location IDs into array
        $scope.locationID = loc_IDs[0];                       // use first location ID as default (if multiple)

    } else {

        window.location.href = './html/Login.html';
    
    }   // if()

    Get_Shop_Locations();

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

        $http.post('./php/Login.php')
        
            .then(
                function(response){
                    console.log("Logged out successfully.");
                    window.location.href = './html/Login.html';
                }
            )
            .catch(
                function(response){
                    console.log("Logout failed.");
                }
            );
    }   // Logout()

}

app.controller("MainController", mainController);
