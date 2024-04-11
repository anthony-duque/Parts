
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

        $scope.ViewCar = function(){
            console.log("In View Car.");
            $rootScope.productionView = 'Car_View.html';
        }
    };

app.controller("TechnicianViewController", TechnicianViewCtrlr);
