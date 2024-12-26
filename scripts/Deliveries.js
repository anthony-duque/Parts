
var DeliveriesCtrlr = function($scope, $http, utility){

    $scope.carsInOut = "all";

    $scope.Get_All_Cars = function(days)
    {
        $http.get('./php/Deliveries_By_Car.php?numDays=' + days)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

    $scope.Get_All_Cars(100);   // default: display all parts

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

    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()
}

app.controller("DeliveriesController", DeliveriesCtrlr);
