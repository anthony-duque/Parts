
var followUpCtlr = function($scope, $http, utility){

    $scope.carsInOut = 'all';

    GetCars();

    function GetCars(){

        $http.get('./php/Follow_Up.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("Follow up Parts List fetched successfully!");
                            console.log(response.data);
                            $scope.vendors = response.data;
                            $scope.estimatorList    = GetEstimatorList();  // for Estimator dropdown
                            $scope.vendorList       = GetVendorList();        // for Vendor dropdown
                            $scope.shopList         = GetShopList();
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Materials list not fetched.");
                    }
             );
    }    // function GetCars()


    function GetEstimatorList(){

        var estimatorList   = [];
        var estimatorName   = 'eStImAtOr';
        var locationID      = 0;

        $scope.vendors.forEach((vendor) => {

            locationID = vendor.locID;

            vendor.cars.forEach((car) => {

                var estimatorFound = false;

                    // is the estimator already in the list?
                estimatorList.forEach((estimator) => {
                    if ((estimator.name == car.estimator) &&
                        (estimator.locID == locationID)){
                        estimatorFound = true;
                    }
                });

                // estimator is not yet in the list
                if (!estimatorFound){
                        // add the estimator to the list
                    var newEstimator = new Object();

                    newEstimator.name   = car.estimator;
                    newEstimator.locID  = locationID;

                    estimatorList.push(newEstimator);
                }   // if(!estimatorList)

            });   //  forEach(car)
        });   // forEach(vendor)

        return estimatorList;
    }   // function GetEstimators()


    function GetVendorList(){

        var vendorList   = [];
        var vendorName   = 'vEnDoR';
        var locationID      = 0;

        $scope.vendors.forEach((vendor) => {

            locationID = vendor.locID;

            if (vendor.name.length > 0){

                var vendorFound = false;

                    // is the estimator already in the list?
                vendorList.forEach((vList) => {
                    if ((vList.name == vendor.name) &&
                        (vList.locID == locationID)){
                        vendorFound = true;
                    }
                });

                    // vendor is not yet in the list
                if (!vendorFound){
                        // add the vendor to the list
                    var newVendor = new Object();

                    newVendor.name   = vendor.name;
                    newVendor.locID  = locationID;

                    vendorList.push(newVendor);
                }   // if(!vendorFound)
            }

        });   // forEach(vendor)

        return vendorList;
    }   // function GetEstimators()


    function GetShopList(){

        var shopList   = [];
        var shopName   = 'sHoP';
        var shopID     = 0;

        $scope.vendors.forEach((vendor) => {

            shopID = vendor.locID;

            if (vendor.locName.length > 0){

                var shopFound = false;

                    // is the estimator already in the list?
                shopList.forEach((shop) => {
                    if ((shop.name == vendor.locName) &&
                        (shop.locID == shopID)){
                        shopFound = true;
                    }
                });

                    // vendor is not yet in the list
                if (!shopFound){

                        // add the vendor to the list
                    var newShop = new Object();

                    newShop.name   = vendor.locName;
                    newShop.locID  = shopID;

                    shopList.push(newShop);
                }   // if(!vendorFound)
            }

        });   // forEach(vendor)

        return shopList;
    }   // function GetEstimators()


    $scope.showHideVendor = function(pStatus, vendorName){

        var hideVendor = false;

        switch(true){

            case ((pStatus == "NOT ORDERED") && (vendorName.length > 0)):
            case ((pStatus == "ORDERED") && (vendorName.length < 1)):
                hideVendor = true;
                break;
            default:
                break;  // ie. hideVendor = false
        }

        return hideVendor;

    }   // showHideVendor()

        // Computes the background color for a row
        // based on ordered, received, and returned quantities.
    $scope.PartStatus = function(objPart){
        return utility.ColorPartStatus(objPart);
    }   // PartStatus()


    $scope.Reset_Filters = function(){

        $scope.vendorName       = "";
        $scope.estimatorName    = "";
        $scope.partStatus       = "";

    }   // Reset_Filters()

}   // sampleCtlr

app.controller("FollowUpController", followUpCtlr);
