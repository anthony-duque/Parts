
var EstimatorViewCtrlr =

    function($scope, $http, utility){

        GetRepairOrders();

        function handleSuccess(response)
        {
            if (response.data){
             console.log("Repair records fetched successfully!");
             console.log(response.data);
             $scope.estimators = response.data;
            }
        }

        function handleError(response)
        {
            console.log("Repair records not fetched.");
        }

        function GetRepairOrders()
        {
            $http.get('./php/Estimator_View.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }
            // changes the background color of car depending on Parts received
        $scope.CheckParts = function(x, y, partsRcvd){
            return utility.CheckPartStatus(x, y, partsRcvd);
        }
    };

app.controller("EstimatorViewController", EstimatorViewCtrlr);
