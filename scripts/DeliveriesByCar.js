
var DeliveriesByCarCtrlr = function($scope, $http){

    $scope.Get_All_Cars = function(days)
    {
        $http.get('./php/Deliveries_By_Car.php?numDays=' + days)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

    $scope.Get_All_Cars($scope.numDays);   // default: display all received
                                //  parts regardless of date
    function handleSuccess(response)
    {
        if (response.data){

            console.log("Received Parts fetched successfully!");
            console.log(response.data);

            $scope.allCars = response.data;
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Received Parts records not fetched.");
    }

}

app.controller("DeliveriesByCarCtrlr", DeliveriesByCarCtrlr);
