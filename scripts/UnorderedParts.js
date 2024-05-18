var app = angular.module("UnorderedPartsApp", []);

function UnorderedPartsCtrlr($scope, $http, utility){

    GetPartsList();

    function GetPartsList()
    {
        $http.get('./php/UnorderedParts.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetUnorderedParts()

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Unordered parts fetched successfully!");
         console.log(response.data);
         $scope.estimators = response.data;
        }
    }   // handleSuccess()

    function handleError(response)
    {
        console.log("Unordered Parts list not fetched.");
    }   // handleError()

    $scope.ToggleRow = function(x){
        return utility.ToggleRows(x);
    }   // CheckParts()

}   // UnorderedPartsCtrlr()

app.controller("UnorderedPartsController", UnorderedPartsCtrlr);
