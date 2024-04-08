
var app = angular
  .module("prodModule", [])
    .controller("prodController", function($scope, $http){

        $scope.productionView = 'All_View.html';

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

        $scope.chooseView = function(name){

            switch(name){

                case 'Sep':
                case 'Jim':
                case 'Tony':
                case 'Anthony':
                case 'Chad':
                    $scope.productionView = 'Estimator_View.html';
                    break;

                case 'Van':
                case 'Jose':
                case 'Jerry':
                case 'Nacho':
                case 'Brian':
                    $scope.productionView = 'Tech_View.html';
                    break;

                default:
                    $scope.productionView = 'All_View.html';
                    break;

            }

        }



    });
