var UnorderedPartsCtrlr =

    function($scope, $http, utility){

        GetUnorderedParts();

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

        function GetUnorderedParts()
        {
            $http.get('./php/Unordered_Parts.php')
                  .then(handleSuccess)
                  .catch(handleError);   // .then()
        }     // GetUnorderedParts()

};  // UnorderedPartsCtrlr()

app.controller("UnorderedPartsController", UnorderedPartsCtrlr);
