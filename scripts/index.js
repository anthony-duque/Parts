var app = angular.module("TabsApp", ['ngCookies']);

var TabsCtrlr = function($scope, $http, $cookies, utility){

    $scope.locationID = '';

    if ($cookies.get('locationID') > ''){

        var loc_IDs = $cookies.get('locationID').split(',');  // split comma-separated location IDs into array
        $scope.locationID = loc_IDs[0];                       // use first location ID as default (if multiple)

    } else {

        window.location.href = './Login.html';
    
    }   // if()

    const DEFAULT_VIEW = 'Stage.html';
    
    $scope.tabView = DEFAULT_VIEW;   // initial tab view

    $scope.dateToday = new Date().toLocaleDateString();

    $scope.Tabs =
        {   
            'Stage'         : "active",
            'Production'    : "inactive",
            'Deliveries'    : "inactive",
            'PartsSearch'   : "inactive",
            'Materials'     : "inactive",
            'ReturnForms'   : "inactive",
            'FollowUp'      : "inactive",
            'Vendors'       : "inactive"
        };

    $scope.tabWidth = 100 / Object.keys($scope.Tabs).length + '%';

    $scope.PickTab = function(tabName){

        for (var key in $scope.Tabs){
            $scope.Tabs[key] = 'inactive';
        }

        $scope.Tabs[tabName] = 'active';

        var tabView;

        switch(tabName){

            case 'Stage':
                tabView = 'Stage.html';
                break;

            case 'Production':
                tabView = 'Production.html';
                break;

            case 'Deliveries':
                tabView = 'Deliveries.html';
                break;

            case 'PartsSearch':
                tabView = 'Parts_Search.html';
                break;

            case 'Materials':
                tabView = 'Order_Materials.html';
                break;

            case 'FollowUp':
                tabView = 'Follow_Up.html';
                break;

            case 'ReturnForms':
                tabView = 'Return_Forms.html';
                break;

            case 'Vendors':
                tabView = 'Vendors.html';
                break;

            default:
                tabView = 'index.html';
                break;
        }

        $scope.tabView = tabView;
    }   // PickTab()

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
                    window.location.href = './Login.html';
                }
            )
            .catch(
                function(response){
                    console.log("Logout failed.");
                }
            );
    }   // Logout()

}

app.controller("TabsController", TabsCtrlr);
