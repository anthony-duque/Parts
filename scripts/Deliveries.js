var app = angular.module("DeliveriesApp", []);

var DeliveriesCtrlr = function($scope, $http){

    $scope.carsInOut = "all";
    Get_All_Cars();

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Repair records fetched successfully!");
         console.log(response.data);
         $scope.allCars = response.data;
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }

    function Get_All_Cars()
    {
        $http.get('./php/Deliveries_By_Car.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }


}

app.controller("DeliveriesController", DeliveriesCtrlr);
