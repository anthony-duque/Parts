var app = angular.module("PartsSearchApp", []);

var PartsSearchCtrlr = function($scope, $http, utility){

    $scope.sortFld = '+ro_num';

    GetAllParts();

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


    function GetAllParts()
    {
        $http.get('./php/Parts_Search.php')
          .then(handleSuccess)
          .catch(handleError);   // .then()
    } // GetAll


    $scope.SortParts = function(sortFld){
        $scope.sortFld = utility.SortField(sortFld, $scope.sortFld);
    }

}

app.controller("PartsSearchController", PartsSearchCtrlr);
