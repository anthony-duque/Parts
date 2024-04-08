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

        //$scope.Vendors = Vendors;   // load from Vendors.js
        GetVendors();

        $scope.SaveNewDelivery = function(){

            console.log('Saving the delivery');

            var len = $scope.Delivery.Items.length;
            for(var i = 0; i < len; ++i){
                if($scope.Delivery.Items[i].Description.trim() == ''){
                    $scope.Delivery.Items.splice(i, 1); // delete last item if empty
                };
            }
            console.log($scope.Delivery);
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

        function handleSuccess(response)
        {
            if (response.data){
             console.log("Vendors fetched successfully!");
             console.log(response.data);
             $scope.Vendors = response.data;
            }
        }

        function handleError(response)
        {
            console.log("Delivery records not fetched.");
            //console.log(response.status);
            //console.log(response.statusText);
            //console.log(response.headers());
        }

        function GetVendors()
        {
            $http.get('./php/mysql_db_open.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
       }

    }       // function($scope, $http)

NewDeliveryModule.controller("NewDeliveryController", NewDeliveryController);