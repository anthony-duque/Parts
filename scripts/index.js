var app = angular.module("TabsApp", []);

var TabsCtrlr = function($scope, $http, utility){

    const DEFAULT_VIEW = 'Production.html';

    $scope.locationID = '';

    $scope.tabView = DEFAULT_VIEW;   // initial tab view

    $scope.dateToday = new Date().toLocaleDateString();

    $scope.Tabs =
        {   'Production'    : "active",
            'Deliveries'    : "inactive",
            'PartsSearch'   : "inactive",
            'Materials'     : "inactive",
            'ReturnForms'   : "inactive",
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
    GetUploadTimeStamp();

    /////////////////////////////////////////////

    function Get_Shop_Locations(){
        $http.get('./php/index.php')
            .then(handleSuccess)
            .catch(handleError);
    }


    function GetUploadTimeStamp(timeObj){

        $http.get('./php/Get_Upload_Time.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("Last Upload Time fetched successfully!");
                            console.log(response.data);

                            const last_update = new Date (response.data);
                            $scope.last_update = last_update.toLocaleString();
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Last Upload Time not fetched.");
                    }
             );
    }    // GetUploadTimeStamp()


    function handleSuccess(response)
    {
        if (response.data){
            console.log(response.data);

                // Last Update Date
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
