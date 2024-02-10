app.factory('InventoryService', GetDeliveries);

    var GetDeliveries = function(){

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Delivery records fetched successfully!");
         console.log(response.data);
         $scope.receivedParts = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Delivery records not fetched.");
        //console.log(response.status);
        //console.log(response.statusText);
        //console.log(response.headers());
    }

    $http.get('./php/Delivery.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
}
