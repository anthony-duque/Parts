var app = angular.module("SampleApp", []);

var sampleCtlr = function($scope, $http){

    $scope.Tabs =
        {   
            'Stage'         : "active",
            'Production'    : "inactive",
            'Deliveries'    : "inactive",
            'PartsSearch'   : "inactive",
            'Materials'     : "inactive",
            'ReturnForms'   : "inactive",
            'PaintList'     : "inactive",
            'FollowUp'      : "inactive",
            'Vendors'       : "inactive"
        };

}   // sampleCtlr

app.controller("SampleController", sampleCtlr);
