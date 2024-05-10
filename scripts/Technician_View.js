
var TechnicianViewCtrlr =

    function($scope, $rootScope, $http, utility){

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

            // changes the background color of car depending on Parts received
        $scope.CheckParts = function(x, y, percentRcvd){
            return utility.CheckPartStatus(x, y, percentRcvd);
        }   // CheckParts()

    };

app.controller("TechnicianViewController", TechnicianViewCtrlr);
