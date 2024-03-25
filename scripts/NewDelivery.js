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

        $scope.Delivery = {
            "RONum": null,
            "VendorID": null,
            "Items":[]
        };

        $scope.Vendors = Vendors;   // load from Vendors.js

        $scope.SaveNewDelivery = function(){

            console.log('Saving the delivery');

            var len = $scope.Deliveries.length;
            for(var i = 0; i < len; ++i){
                if($scope.Deliveries[i].Description.trim() == ''){
                    $scope.Deliveries.splice(i, 1); // delete last item if empty
                };
            }
            console.log($scope.Deliveries);
        }

        $scope.Add_Item = function(){
            Item = {"Description":"", "Location":"", "Rack": ''};
            $scope.Delivery.Items.push(Item);
        }

        $scope.GetROInfo = function(roNum){

            $scope.Delivery.RONum = roNum;

            for(var i = 0; i < RepairOrders.length; ++i){
                if(RepairOrders[i].Number == roNum){
                    $scope.RO.Owner = RepairOrders[i].Owner;
                    $scope.RO.Vehicle = RepairOrders[i].Vehicle;
                    $scope.RO.Found = true;
                    break;
                }
            }   // for (var i = 0...)
        }   // GetROInfo()
    }       // function($scope, $http)

NewDeliveryModule.controller("NewDeliveryController", NewDeliveryController);
