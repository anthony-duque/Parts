var app = angular.module("UploadVendorsApp", []);

var uploadVendorsCtrlr = function($scope, $http){

    Get_Shop_Locations();

    function Get_Shop_Locations(){
        $http.get('./php/index.php')
            .then(handleSuccess)
            .catch(handleError);
    }   // Get_Shop_Locations()


    function handleSuccess(response)
    {
        if (response.data){
            console.log(response.data);
            $scope.locations = response.data.locations;
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Shop locations not fetched.");
    }   // handleError()

}   // uploadVendorsCtrlr()

app.controller("UploadVendorsCtrlr", uploadVendorsCtrlr);
