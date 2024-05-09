var app = angular.module("TabsApp", []);

var TabsCtrlr = function($scope){

    $scope.Tabs =
        { 'Production':"active", 'PartSearch':"inactive", 'PaintList': "inactive", 'Upload':"inactive" };

    $scope.PickTab = function(tabName){
        for (var key in $scope.Tabs){
            $scope.Tabs[key] = 'inactive';
        }
        $scope.Tabs[tabName] = 'active'
    }   // PickTab()

}

app.controller("TabsController", TabsCtrlr);
