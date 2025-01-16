var matAdminApp = angular.module("MatAdminApp", []);

var matAdminCtlr = function($scope, $http){

    $scope.x = "xxx";

    var material = {
        "part_number"   : "",
        "description"   : "",
        "unit"          : '',
        "reorder_qty"   : 0
    };

    $scope.newMaterial = material;

    GetMaterialsList();
    $scope.materialsList = [];

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

    }     // GetCarList()

}   // SampleController()

matAdminApp.controller("MatAdminCtlr", matAdminCtlr);
