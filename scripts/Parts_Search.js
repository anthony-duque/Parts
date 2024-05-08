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
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Parts records not fetched.");
    }   // handleError()


    function GetAllParts()
    {
        $http.get('./php/Parts_Search.php')
          .then(handleSuccess)
          .catch(handleError);   // .then()
    } // GetAllParts()


    $scope.SortParts = function(sortFld){
        $scope.sortFld = utility.SortField(sortFld, $scope.sortFld);
    }   // SortParts()

}

app.controller("PartsSearchController", PartsSearchCtrlr);
