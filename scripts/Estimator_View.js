
var EstimatorViewCtrlr =

    function($scope, $http){

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
            //console.log(response.status);
            //console.log(response.statusText);
            //console.log(response.headers());
        }

        function GetRepairOrders()
        {
            $http.get('./php/Estimator_View.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }

        $scope.CheckParts = function(x, y, partsRcvd){

            var bgColor = '';

            switch(true){

                case (partsRcvd == 1):
                    bgColor = 'partsComplete';
                    break;

                case (((x + y) % 2) == 1):
                    bgColor = 'lightBlue';  // temporary until actual status is computed
                    break;

                default:
                    bgColor = 'white';
                    break;
            }

            return bgColor;
        }
    };

app.controller("EstimatorViewController", EstimatorViewCtrlr);
