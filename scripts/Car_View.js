var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http){

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

/*
    function GetAllPartsForRO(ROnum)
    {
        $http.get('./php/Car_View.php?roNum=' + ROnum)
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }     // GetAllPartsForRO()
*/
    $scope.GoBackToMainPage = function(){
        self.close();
        opener.location.reload();
    }


    $scope.sortField = "+ordered_quantity";

    $scope.SortParts = function(sortBy){

       switch($scope.sortOrder){

          case '+':
             $scope.sortOrder = '-';
             break;

          case '-':
             $scope.sortOrder = '+';
             break;

          default:
             $scope.sortOrder = '+';
             break;

       } // switch()

       $scope.sortField = $scope.sortOrder + sortBy;
    }  // SortPatients()

        // Computes the background color for a row
        // based on ordered, received, and returned quantities.
    $scope.PartStatus = function(ro_qty, ord_qty, rcvd_qty, ret_qty, ven_name, part_num){

        var bkgrnd_class = '';  // Background class

        switch (true) {

            case (ven_name.length == 0):
                bkgrnd_class = "partsComplete";
                break;

            case ro_qty == 0:
                bkgrnd_class = "partsComplete";
                break;

            case (ret_qty == rcvd_qty) && (ret_qty > 0):
                bkgrnd_class = "noParts";
                break

            case (rcvd_qty == ro_qty) && (ro_qty > 0):
                bkgrnd_class = "partsComplete";
                break;

        //    case (rcvd_qty == ord_qty) && (rcvd_qty > 0):
        //        bkgrnd_class = "partsComplete";
        //        break;

            case (ord_qty > 0) && (rcvd_qty == 0):
                bkgrnd_class = "waitingForParts";
                break;

            default:
                bkgrnd_class = "noParts";
                break
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
         console.log(response.data);
         $scope.car = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Car parts records not fetched.");
    }

}

app.controller("carViewController", carViewCtrlr);
