var app = angular.module("TabsApp", []);

var TabsCtrlr = function($scope, $http){

    const DEFAULT_VIEW = 'Production.html';

    $scope.tabView = DEFAULT_VIEW;   // initial tab view

    $scope.dateToday = new Date().toLocaleDateString();

    $scope.Tabs =
        {   'Production':"active",
            'Deliveries':"inactive",
            'PartsSearch':"inactive",
            'PaintList': "inactive",
//            'Upload':"inactive",
            'UnorderedParts': "inactive"
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

            case 'PaintList':
                tabView = 'Paint_List.html';
                break;

            case 'UnorderedParts':
                tabView = 'Unordered_Parts.html';
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
//         console.log("Last update fetched successfully!");
         console.log(response.data);
         const last_update = new Date (response.data.last_update);
         $scope.last_update = last_update.toLocaleString();
        }
    }

    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }

}

app.controller("TabsController", TabsCtrlr);
