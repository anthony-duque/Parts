
var EstimatorViewCtrlr =

    function($scope, $http){
        $scope.sample = "Estimators";

        GetRepairOrders();

        function handleSuccess(response)
        {
            if (response.data){
             console.log("Repair records fetched successfully!");
             console.log(response.data);
             $scope.repairOrders = response.data;
            }
        }

        function handleError(response)
        {
            console.log("Repair records not fetched.");
            //console.log(response.status);
            //console.log(response.statusText);
            //console.log(response.headers());
        }

        function GetRepairOrders()
        {

            $http.get('./php/Production.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }

    };

app.controller("EstimatorViewController", EstimatorViewCtrlr);
