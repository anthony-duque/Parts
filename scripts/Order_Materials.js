
var OrderMaterialsCtlr = function($scope, $http, utility){

    $scope.techList = [
        "" ,"Serjio", "Jose", "Van", "Nacho", "Gerry", "Omar", "Nick", "Jesus", "Frank (Paint)", "Frank (Tech)", "Eric"
    ];

    $scope.materialsList    = [];   // material list from the database
    $scope.newMatList       = [];   // materials that are not in the database
    $scope.ordersList       = [];   // list of ordered materials

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


    $scope.Add_New_Material = function(){

        var newMat = {
            "part_number":  "",
            "description":  "",
            "ordered_qty":  0,
            "unit"  :   ""
        }

        $scope.newMatList.unshift(newMat);   // add new material to the materials list

    }   // Add_New_Material()


    $scope.SubmitOrder = function(){

        var order = {
            "technician": $scope.technician,
            "materials" : $scope.ordersList
        }

        $http.post('./php/Materials_Ordered.php', JSON.stringify(order))
            .then(
                function(response){     // successful POST
                    console.log(response.data);
                    if (response.data.search("successful") > -1){
                        alert("Request sent successfully!");
                        // Reset 1) Orders  2) Quantities  3) Tech
                        $scope.ordersList = [];
                        $scope.newMatList = [];
                        $scope.materialsList.forEach(function(eachMat){
                            eachMat.ordered_qty = 0;
                        });
                        $scope.technician = "";
                    }
                },
                function(response){     // failed POST
                    console.log("Service does not exist");
                    console.log(response.status);
                    console.log(response.statusText);
                    console.log(response.headers());
                }
            );  // then()
    }   // SubmitOrder()


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


    $scope.CheckIfOrdered = function(qty){

        var checkMark = '';

        if(qty > 0){
            checkMark = '\u2713';
        }

        return checkMark;
    }   // CheckIfOrdered()
};

app.controller("OrderMaterialsController", OrderMaterialsCtlr);
