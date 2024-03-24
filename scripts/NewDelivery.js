var NewDeliveryModule = angular.module("NewDeliveryModule", []);

var NewDeliveryController =
        function($scope, $http){
                    $scope.sample = "Sample";
        }

NewDeliveryModule.controller("NewDeliveryController", NewDeliveryController);
