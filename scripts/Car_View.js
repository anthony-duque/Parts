var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http, utility){

        // get the value of ro num from the queryString
    var params = getQueryParams(window.location.href);
    $scope.roNum = params.roNum;

    var GetAllPartsForRO = function(ROnum)
    {
        $http.get('./php/Car_View.php?roNum=' + ROnum)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetAllPartsForRO()

    GetAllPartsForRO($scope.roNum);

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
    $scope.PartStatus = function(ro_qty, ord_qty, rcvd_qty, ret_qty, ven_name, part_num){

        var bkgrnd_class = '';  // Background class

        switch (true) {

            case (ven_name.length == 0) && (part_num.length == 0):
                bkgrnd_class = "partsComplete";
                break;

            case (rcvd_qty == 0) && (ord_qty == 0) && (ro_qty > 0):
                bkgrnd_class = "noParts";
                break;

            case (rcvd_qty == ret_qty) && (ret_qty > 0):
            case (rcvd_qty == 0) && ((ord_qty > 0) || (ro_qty > 0)):
                bkgrnd_class = "waitingForParts";
                break;

            default:
                bkgrnd_class = "partsComplete";
                break;

        }   // switch()

        return bkgrnd_class;
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
