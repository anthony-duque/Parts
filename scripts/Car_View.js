var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http){

    var params = getQueryParams(window.location.href);

    var roNum = params.roNum;

    GetAllPartsForRO(roNum);

    function getQueryParams(url) {

      const queryString = url.split('?')[1];
      const params = {};
      if (queryString) {
        queryString.split('&').forEach((param) => {
          const [key, value] = param.split('=');
          params[key] = decodeURIComponent(value);
        });
      }
      return params;
    }

    function handleSuccess(response)
    {
        if (response.data){
         console.log("Car Parts records fetched successfully!");
         console.log(response.data);
         $scope.car = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Car parts records not fetched.");
        //console.log(response.status);
        //console.log(response.statusText);
        //console.log(response.headers());
    }

    function GetAllPartsForRO(roNum)
    {
        $http.get('./php/Car_View.php?roNum=' + roNum)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

}

app.controller("carViewController", carViewCtrlr);
