var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http, utility){

        // get the value of ro num from the queryString
    var params = getQueryParams(window.location.href);
    $scope.roNum = params.roNum;
    $scope.locationID = params.locationID;

    function GetAllPartsForRO(ROnum, locationID)
    {
        $http.get('./php/Car_View.php?roNum=' + ROnum + '&locationID=' + locationID)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetAllPartsForRO()

    GetAllPartsForRO($scope.roNum, $scope.locationID);

    $scope.GoBackToMainPage = function(){
        self.close();
        opener.location.reload();
    }

    $scope.sortField = "+received_quantity"; // initially sort list by received qty

    $scope.SortParts = function(sortFld){
        $scope.sortField = utility.SortField(sortFld, $scope.sortField);
    }  // SortParts()

        // Computes the background color for a row
        // based on ordered, received, and returned quantities.
    $scope.PartStatus = function(objPart){
        return utility.ColorPartStatus(objPart);
    }   // PartStatus()

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
         console.log(response.data);  // uncomment for troubleshooting
         $scope.car = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Car parts records not fetched.");
    }

}

app.controller("carViewController", carViewCtrlr);
