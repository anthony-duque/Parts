
var DeliveriesCtrlr = function($scope, $http, utility){

    $scope.carsInOut = "all";
    $scope.viewBy = "DeliveriesByCar.html";
    $scope.numDays = 365;

    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()

    $scope.Get_All_Parts = function(numOfDays){

        $scope.numDays = numOfDays;

        switch($scope.viewBy){

            case "DeliveriesByCar.html":
                $scope.Get_All_Cars($scope.numDays);
                break;

            case "DeliveriesByVendor.html":
                $scope.Get_All_Vendors($scope.numDays);
                break;

            default:
                break;
        }   // switch()
    }   // Get_All_Parts()

    $scope.Get_All_Vendors = function(days)
    {
        $http.get('./php/Deliveries_By_Vendor.php?numDays=' + days)
              .then(getVendorPartsSuccess)
              .catch(getVendorPartsError);   // .then()
    }
                                //  parts regardless of date
    function getVendorPartsSuccess(response)
    {
        if (response.data){

            console.log("Vendor Parts fetched successfully!");
            console.log(response.data);

            $scope.allVendors = response.data;
        }
    }   // handleSuccess()

    function getVendorPartsError(response)
    {
        console.log("Vendor Parts records not fetched.");
    }

    $scope.Get_All_Cars = function(days)
    {
        $http.get('./php/Deliveries_By_Car.php?numDays=' + days)
              .then(getCarPartsSuccess)
              .catch(getCarPartsError);   // .then()
    }

    function getCarPartsSuccess(response)
    {
        if (response.data){

            console.log("Car Parts fetched successfully!");
            console.log(response.data);

            $scope.allCars = response.data;
        }
    }   // handleSuccess()

    function getCarPartsError(response)
    {
        console.log("Car Parts records not fetched.");
    }

}

app.controller("DeliveriesController", DeliveriesCtrlr);
