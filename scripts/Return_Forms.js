var app = angular.module("ReturnFormsApp", []);

var returnFormsCtrlr = function($scope, $http, utility){

    Get_Return_Forms();

    function handleSuccess(response)
    {
        if (response.data){
            console.log("Return forms fetched successfully!");
            console.log(response.data);
            $scope.returnForms = response.data;
            var indx = $scope.returnForms.indexOf("..");
            $scope.returnForms.splice(indx, 1);    // remove ".."
            Get_Pending_Returns();
        }
    }   // handleSuccess()


    function Pending_Returns_Success(response){

        if (response.data){
            console.log("Pending Returns fetched successfully!");
            console.log(response.data);
            $scope.pendingReturns = response.data;
            Find_Returns_With_Forms();
        }
    }

    function handleError(response, rec_name)
    {
        console.log(rec_name + " not fetched.");
    }   // handleError()


    function Get_Return_Forms()
    {
        $http.get('./php/Return_Forms.php')
              .then(handleSuccess)
              .catch(handleError("Return Forms"));
    }     // GetRepairOrders()

    function Get_Pending_Returns()
    {
        $http.get('./php/Pending_Returns.php')
              .then(Pending_Returns_Success)
              .catch(handleError("Pending Returns"));
    }     // GetRepairOrders()


    function Find_Returns_With_Forms()
    {
        var returnNum = '';
        for(var i = 0; i < $scope.returnForms.length; ++i){
            returnNum = $scope.returnForms[i].split(".")[0];
            for(var j = 0; j < $scope.pendingReturns.length; ++j){
                if($scope.pendingReturns[j].return_num == returnNum){
                    $scope.pendingReturns[j].return_form = $scope.returnForms[i];
                    break;
                }
            }
        }
        console.log($scope.pendingReturns);
    }   // Find_Returns_With_Forms()

    $scope.sortField = "+ro_num"; // initially sort list by received qty

    $scope.SortParts = function(sortFld){
        $scope.sortField = utility.SortField(sortFld, $scope.sortField);
    }  // SortParts()


}   // returnFormsCtrlr

app.controller("ReturnFormsCtrlr", returnFormsCtrlr);
