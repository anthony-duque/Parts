var app = angular.module("OrderMaterialsApp", []);

var OrderMaterialsCtrlr = function($scope, $http){

    $scope.technicians = [
        "" ,"Serjio", "Jose", "Van", "Nacho", "Gerry"
    ];

    $scope.materialsList = [];
    GetMaterialsList();

    function GetMaterialsList()
    {
        $http.get('./php/Materials.php')
              .then(
                    function(response){
                        if (response.data){
                            console.log("Materials List fetched successfully!");
                            console.log(response.data);
                            $scope.materialsList = response.data;
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Materials list not fetched.");
                    }
             );
    }     // GetMaterialsList()

};

app.controller("OrderMaterialsController", OrderMaterialsCtrlr);
