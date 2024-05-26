
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
                  .catch(handleError);   // .then()
        }     // GetRepairOrders()


            // changes the background color of car depending on Parts received
        $scope.CheckParts = function(car){

            var bgClass = '';
            var pStatus = '';   // part status
            var pWaiting = 0;
            var pUnordered = 0;
            var pReceived = 0;

            car.parts.forEach(

                function(carPart, index){
                    pStatus = utility.ColorPartStatus(carPart);
                    switch(pStatus){

                        case 'noParts':
                            ++pUnordered;
                            break;

                        case 'waitingForParts':
                            ++pWaiting;
                            break;

                        default:
                            ++pReceived;
                            break;
                    }
            }); // car.parts.forEach()

            switch(true){

                case (pUnordered > 0) && (pWaiting > 0):
                case (pUnordered > 0) && (pReceived > 0):
                    bgClass = 'orange';
                    break;

                case (pUnordered > 0) && (pReceived == 0):
                    bgClass = 'noParts';
                    break;

                case (pWaiting > 0):
                    bgClass = 'waitingForParts';
                    break;

                default:
                    bgClass = 'partsComplete';
                    break;

            }   // switch(true)

            return bgClass;
        }   // CheckParts()
};

app.controller("EstimatorViewController", EstimatorViewCtrlr);
