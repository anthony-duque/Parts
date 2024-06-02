
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
        }   // handleSuccess()


        function handleError(response)
        {
            console.log("Repair records not fetched.");
        }   // handleError()


        function GetRepairOrders()
        {
            $http.get('./php/Estimator_View.php')
                  .then(handleSuccess)
                  .catch(handleError);
        }     // GetRepairOrders()


            // changes the background color of car depending on Parts received
        $scope.CheckParts = function(car){

            return utility.ColorCarPartsStatus(car);

        }   // CheckParts()
};

app.controller("EstimatorViewController", EstimatorViewCtrlr);
