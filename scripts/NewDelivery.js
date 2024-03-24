var NewDeliveryModule = angular.module("NewDeliveryModule", []);

var NewDeliveryController =

    function($scope, $http){

        $scope.sample = "Sample";

        $scope.RO = {
            Number: null,
            Vehicle: null,
            Owner: null,
            Found: false
        };

        $scope.GetROInfo = function(roNum){

            for(var i = 0; i < RepairOrders.length; ++i){
                if(RepairOrders[i].Number == roNum){
                    $scope.RO.Owner = RepairOrders[i].Owner;
                    $scope.RO.Vehicle = RepairOrders[i].Vehicle;
                    $scope.RO.Found = true;
                }
            }   // for (var i = 0...)
        }   // GetROInfo()
    }       // function($scope, $http)

NewDeliveryModule.controller("NewDeliveryController", NewDeliveryController);
