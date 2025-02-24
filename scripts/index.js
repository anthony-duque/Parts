var app = angular.module("TabsApp", []);

var TabsCtrlr = function($scope, $http, utility){

    const DEFAULT_VIEW = 'Production.html';

    $scope.locationID = 0;

    $scope.tabView = DEFAULT_VIEW;   // initial tab view

    $scope.dateToday = new Date().toLocaleDateString();

    $scope.Tabs =
        {   'Production'    : "active",
            'Deliveries'    : "inactive",
            'PartsSearch'   : "inactive",
            'Materials'     : "inactive",
            'PaintList'     : "inactive",
            'UnorderedParts': "inactive",
            'Vendors'       : "inactive"
         };

    $scope.PickTab = function(tabName){

        for (var key in $scope.Tabs){
            $scope.Tabs[key] = 'inactive';
        }

        $scope.Tabs[tabName] = 'active';

        var tabView;

        switch(tabName){

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

            case 'PaintList':
                tabView = 'Paint_List.html';
                break;

            case 'UnorderedParts':
                tabView = 'Unordered_Parts.html';
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

    GetLastUpdate();

    function GetLastUpdate(){
        $http.get('./php/index.php')
            .then(handleSuccess)
            .catch(handleError);
    }

    function handleSuccess(response)
    {
        if (response.data){
            console.log(response.data);

                // Last Update Date
            const last_update = new Date (response.data.last_update);
            $scope.last_update = last_update.toLocaleString();
            $scope.locations = response.data.locations;

        }
    }

    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()

}

app.controller("TabsController", TabsCtrlr);
