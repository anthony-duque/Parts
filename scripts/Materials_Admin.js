var matAdminApp = angular.module("MatAdminApp", []);

var matAdminCtlr = function($scope, $http){

    var material = {
        "part_number"   : "",
        "description"   : "",
        "unit"          : '',
        "reorder_qty"   : 0
    };

    $scope.newMaterial = material;

    $scope.materialsList = [];

    GetMaterialsList();

    $scope.Add_Material = function(){
//        console.log($scope.newMaterial);
////////////
    $http.post('./php/Materials.php', JSON.stringify($scope.newMaterial))
        .then(function(response) {
             if (response.data){
                console.log(response.data);
                //console.log("New Material written to database!");
             }
          },
          function(response) {
             console.log("Service does not Exists");
             console.log(response.status);
             console.log(response.statusText);
             console.log(response.headers());
         });

////////////
    }


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
