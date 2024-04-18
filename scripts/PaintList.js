var paintListApp = angular.module("PaintListApp", []);

var paintListCtrlr = function($scope, $http){

    GetCarList();

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Car records fetched successfully!");
         console.log(response.data);
         $scope.techList = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }

    function GetCarList()
    {
        $http.get('./php/Technician_View.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

}   // paintListCtrlr()

paintListApp.controller("PaintListController", paintListCtrlr);
