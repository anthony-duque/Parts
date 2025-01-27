var app = angular.module("OrderMaterialsApp", []);

var OrderMaterialsCtrlr = function($scope, $http){

    $scope.techList = [
        "" ,"Serjio", "Jose", "Van", "Nacho", "Gerry"
    ];

    $scope.materialsList    = [];
    $scope.ordersList       = [];

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


    $scope.CheckOrder = function(matObj){

        var itemIndex = -1;

        $scope.ordersList.forEach((item, i) => {

            if(item.part_number == matObj.part_number){
                itemIndex = i;
            }
        });

        if (itemIndex == -1){   // item not yet ordered
                                // add it to the list
            $scope.ordersList.push(matObj);

        } else {                // item already ordered

            if (matObj.ordered_qty == 0){   // if ordered quantity = 0
                                            // remove it from the Orders List
                $scope.ordersList.splice(itemIndex, 1);

            } else {    // if ordered quantity is greater than 0
                        // just update the quantity.

                $scope.ordersList[itemIndex].ordered_qty = matObj.ordered_qty;
            }
        }
    }   // CheckOrder()

    $scope.SubmitOrder = function(){

        var order = {
            "technician": $scope.technician,
            "materials" : $scope.ordersList
        }

        $http.post('./php/Materials_Ordered.php', JSON.stringify(order))
            .then(
                function(response){     // successful POST
                    console.log(response.data);
                },
                function(response){     // failed POST
                    console.log("Service does not exist");
                    console.log(response.status);
                    console.log(response.statusText);
                    console.log(response.headers());
                }
            );  // then()
    }   // SubmitOrder()
};

app.controller("OrderMaterialsController", OrderMaterialsCtrlr);
