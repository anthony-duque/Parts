var paintListApp = angular.module("PaintListApp", []);

var paintListCtrlr = function($scope, $http){

    $scope.paintList = [];

    GetCarList();

    function GetCarList()
    {
        $http.get('./php/Technician_View.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

    $scope.AddCarToPaintList = function (car, techIndex){

        var carObj = {
            "techIndex": techIndex,
            "car": car
        };

        $scope.paintList.push(carObj);
        $scope.techList.splice(techIndex, 1);
    }


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


}   // paintListCtrlr()

paintListApp.controller("PaintListController", paintListCtrlr);
