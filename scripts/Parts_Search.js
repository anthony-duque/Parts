var app = angular.module("PartsSearchApp", []);

var PartsSearchCtrlr = function($scope, $http){

    GetAllRepairParts();

    function handleSuccess(response)
    {
        if (response.data){
         console.log("All parts records fetched successfully!");
         console.log(response.data);
         $scope.allParts = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Parts records not fetched.");
    }


    function GetAllRepairParts()
    {
        $http.get('./php/Parts_Search.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

}

app.controller("PartsSearchController", PartsSearchCtrlr);
