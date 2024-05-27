var app = angular.module("TabsApp", []);

var TabsCtrlr = function($scope){

    const DEFAULT_VIEW = 'Production.html';

    $scope.tabView = DEFAULT_VIEW;   // initial tab view

    $scope.Tabs =
        {   'Production':"active",
            'PartsSearch':"inactive",
            'PaintList': "inactive",
            'Upload':"inactive",
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

            case 'PartsSearch':
                tabView = 'Parts_Search.html';
                break;

            case 'PaintList':
                tabView = 'Paint_List.html';
                break;

            case 'Upload':
                tabView = 'Admin.html';
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

}

app.controller("TabsController", TabsCtrlr);
