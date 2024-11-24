var app = angular.module("VendorViewModule", []);

var vendorViewCtrlr = function($scope, $http){

	var GetVendorList = function()
    {
        $http.get('./php/Vendors.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetAllPartsForRO()

    GetVendorList();

	function handleSuccess(response)
    {
        if (response.data){
         console.log("Vendor List fetched successfully!");
         console.log(response.data);  // uncomment for troubleshooting
         $scope.vendorList = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Vendor List not fetched.");
    }

}

app.controller("VendorViewController", vendorViewCtrlr);
