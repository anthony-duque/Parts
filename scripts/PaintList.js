var paintListApp = angular.module("PaintListApp", []);

var paintListCtrlr = function($scope, $http){

    GetCarList();

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Car records fetched successfully!");
         console.log(response.data);
         $scope.carList = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Repair records not fetched.");
        //console.log(response.status);
        //console.log(response.statusText);
        //console.log(response.headers());
    }

    function GetCarList()
    {
        $http.get('./php/Technician_View.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

}

paintListApp.controller("PaintListController", paintListCtrlr);
