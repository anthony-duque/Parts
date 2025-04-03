var app = angular.module("ReturnsApp", []);

var returnsController = function($scope, $http){

    Get_Pending_Returns();

    function Get_Pending_Returns(){
        $http.get('./php/Parts_Returns.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Parts Returns records fetched successfully!");
         console.log(response.data);  // uncomment for troubleshooting
         $scope.returns = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Car parts records not fetched.");
    }


}   // returnsController()

app.controller("ReturnsController", returnsController);
