
var TechnicianViewCtrlr =

    function($scope, $rootScope, $http){

        GetRepairOrders();

        function handleSuccess(response)
        {
            if (response.data){
             console.log("Repair records fetched successfully!");
             console.log(response.data);
             $scope.technicians = response.data;
            }
        }

        function handleError(response)
        {
            console.log("Repair records not fetched.");
        }

        function GetRepairOrders()
        {
            $http.get('./php/Technician_View.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }

        $scope.CheckParts = function(x, y, percentRcvd){

            var bgColor = '';
/*
            switch(true){

                case x == 0:
                    bgColor = 'noParts';
                    break;

                case x == 1:
                    bgColor = 'partsComplete';
                    break;

                default:
                    bgColor = 'waitingForParts';
                    break;
            }
*/
            switch(true){

                case (percentRcvd == 1):
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
        }   // CheckParts()

    };

app.controller("TechnicianViewController", TechnicianViewCtrlr);
