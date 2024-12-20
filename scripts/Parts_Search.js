var PartsSearchCtrlr = function($scope, $http, utility){

    $scope.sortFld = '+ro_num';

    $scope.carsInOut = 'all';

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

    $scope.PartStatus = function(partInfo){

        return utility.ColorPartStatus(partInfo);

    }   // PartStatus()

    $scope.SortParts = function(sortFld){

        $scope.sortFld = utility.SortField(sortFld, $scope.sortFld);
    }   // SortParts()

}

    // To filter a list of parts by status (Ordered, Received, Not Ordered)
app.filter('PartsByStatus', function(utility){

    return function(parts, status){

        var filteredParts = [];

        if (status){
            angular.forEach(parts, function(part){
                if (part.part_status == status){
                    filteredParts.push(part);
                }
            });
        } else {
            filteredParts = parts;
        }
        return filteredParts;

    }   // function(parts, status)
});

app.controller("PartsSearchController", PartsSearchCtrlr);
