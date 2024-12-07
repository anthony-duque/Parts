var app = angular.module("VendorViewModule", []);

var vendorViewCtrlr = function($scope, $http, utility){

	$scope.sortFld = '+name';

	var GetVendorList = function()
    {
        $http.get('./php/Vendors.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetAllPartsForRO()

    GetVendorList();
/*** End of Main Routine ***/

	$scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


	$scope.SortVendors = function(sortFld){

        $scope.sortFld = utility.SortField(sortFld, $scope.sortFld);
    }   // SortParts()


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
