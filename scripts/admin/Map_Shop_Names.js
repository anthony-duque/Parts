var app = angular.module("Map_Shop_Names_Page", []);

var mapShopNamesCtlr = function($scope, $http, $window){

    $scope.companyID = $window.sessionStorage.getItem('companyID');

    $scope.shopList = [];   // shopList will have an index if the database shop name
                            // matches a shop name in the CSV extract
                            // for each shop in db_shops; 
                            // set to -1 if there is no match

    Get_Shop_Names($scope.companyID);

    function Get_Shop_Names(companyID){

        $http.get('../../php/admin/Map_Shop_Names.php?companyID=' + companyID)
          .then(handleSuccess)
          .catch(handleError);   // .then()
 
    }   // Get_DB_Shops()


    function handleSuccess(response)
    {
        if (response.data){

            console.log("All Shop Name records fetched successfully!");
            console.log(response.data);

            $scope.db_shops = response.data.db_shops;
            $scope.csv_shops = response.data.csv_shops;


              // Initialize each index of shopList with a new Shop instance        
            $scope.db_shops.forEach((dbShop, dbIndex) => {
                $scope.shopList[dbIndex] = $scope.csv_shops.indexOf(dbShop);
            });

        }   // if(response.data)

    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Shop names not fetched.");
    }   // handleError()
    

        // Uncheck selected shop in the other radio button group when a shop is selected in one group
    $scope.uncheck_shop_in_other_groups = function(dbShopIndex, csvShopIndex){

        $scope.shopList.forEach((shop, i, shopList) => {

            if (i !== dbShopIndex && parseInt(shop) === parseInt(csvShopIndex)) {
                  // If the same shop is selected in both groups, uncheck it in the other group
                shopList[i] = -1;  // Uncheck the shop in the other group
            }
        });
    };

    $scope.FormPostURL = function(url){

        return url + "?companyID=" + $scope.companyID;

    }   // FormPostURL()

    $scope.toJson = angular.toJson;

}   // sampleCtlr

app.controller("Map_Shop_Names_Ctrlr", mapShopNamesCtlr);
