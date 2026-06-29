var app = angular.module("SampleApp", []);

var sampleCtlr = function($scope, $http){

        // will be read from the database in the actual application; hardcoded here for testing purposes
    $scope.db_shops =
        [
            'Caliber Collision 1',
            'Caliber Collision 2',
            'Caliber Collision 3'
        ];

        // will be read from the CSV extract in the actual application; hardcoded here for testing purposes
    $scope.csv_shops =
        [
            'Caliber Collision 1',
            'Caliber Collision - Oxnard',
            'Caliber Collision 3'
        ];


    $scope.shopList = [];  // Initialize shopList as an empty array]

    $scope.db_shops.forEach((dbShop, dbIndex) => {

        $scope.shopList[dbIndex] = $scope.csv_shops.indexOf(dbShop);  // Initialize each index of shopList with a new Shop instance
 
    });

        // Uncheck selected shop in the other radio button group when a shop is selected in one group
    $scope.uncheck_shop_in_other_groups = function(dbShopIndex, csvShopIndex){

        $scope.shopList.forEach((shop, i, shopList) => {

            if (i !== dbShopIndex && parseInt(shop) === parseInt(csvShopIndex)) {
                  // If the same shop is selected in both groups, uncheck it in the other group
                shopList[i] = -1;  // Uncheck the shop in the other group
            }
        });
    };

}   // sampleCtlr

app.controller("SampleController", sampleCtlr);
