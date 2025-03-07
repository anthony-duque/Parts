var app = angular.module("MatAdminApp", []);

var matAdminCtlr = function($scope, $http, utility){

    $scope.sortFld = '+brand';

    function Material(){    // Material Class constructor
        this.part_number = "";
        this.brand = "";
        this.description = "";
        this.unit = "";
        this.reorder_qty = 0;
    }

    $scope.newMaterial = new Material();

    $scope.materialsList = [];
    GetMaterialsList();

    $scope.matTypeList = [];
    GetMaterialTypeList();

    ////////////

    $scope.Add_Material = function(){

        var noMatchingPartNum = $scope.materialsList.every(
            function(eachMat){
                return ($scope.newMaterial.part_number != eachMat.part_number);
            }
        );

        $scope.duplicatePart = $scope.newMaterial.part_number + " already exists!";

        if (noMatchingPartNum){

            $scope.duplicatePart = '';
            console.log($scope.newMaterial);
            $http.post('./php/Materials.php', JSON.stringify($scope.newMaterial))
                    .then(
                        function(response) {
                            if (response.data){
                                console.log(response.data);
                                GetMaterialsList();
                                $scope.newMaterial = new Material();
                            }
                        },
                        function(response) {
                            console.log("Service does not Exists");
                            console.log(response.status);
                            console.log(response.statusText);
                            console.log(response.headers());
                        });
        }  // if (noMatchingPartNum)

    }   // Add_Material()


    function GetMaterialTypeList(){
        $http.get('./php/Material_Types.php')
              .then(
                    function(response){
                        if (response.data){
                            console.log("Material Types List fetched successfully!");
                            console.log(response.data);
                            $scope.materialTypesList = response.data;
                        }    $scope.materialsList = [];
    GetMaterialsList();

                    }
              )         // then()
              .catch(

                function(response){
                    console.log("Material Types list not fetched.");
                }
             );
    }    // GetMaterialTypeList()


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


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()

    $scope.SortMaterials = function(sortFld){
        $scope.sortFld = utility.SortField(sortFld, $scope.sortFld);
    }   // SortParts()

}   // matAdminCtlr()

app.controller("MatAdminCtlr", matAdminCtlr);
