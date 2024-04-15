
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
            //console.log(response.status);
            //console.log(response.statusText);
            //console.log(response.headers());
        }

        function GetRepairOrders()
        {
            $http.get('./php/Technician_View.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }
/*
        $scope.ViewCar = function(){
            $rootScope.productionView = 'Car_View.html';
        }
*/
        $scope.CheckParts = function(x){

            var bgColor = '';

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
            return bgColor;
        }   // CheckParts()

    };

app.controller("TechnicianViewController", TechnicianViewCtrlr);
