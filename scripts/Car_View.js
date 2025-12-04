var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http, utility){

    $scope.showSublet = false;
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

/////////////////////////////////////////////////////////////

    $scope.GoBackToMainPage = function(){
        self.close();
//        opener.location.reload();
    }


    $scope.sortField = "+received_quantity"; // initially sort list by received qty


    $scope.BG_SubletsStatus = function(subletList){

        var bgClass = "waitingForParts";

        if ((subletList == null) || (subletList.length == 0)){

            bgClass = "lightGreen";

        } else {

            var count_done = 0;

            subletList.forEach((sublet, i) => {
                if(sublet.received_quantity > 0){
                    ++count_done;
                }
            });

            switch (true) {

                case (count_done == 0):
                    bgClass = "whiteOnRed";
                    break;

                case (count_done == subletList.length):
                    bgClass = "lightGreen";
                    break;

                default:
                    break;
            }
        }

        return bgClass;
    }   // BG_SubletsStatus()


    $scope.SortParts = function(sortFld){
        $scope.sortField = utility.SortField(sortFld, $scope.sortField);
    }  // SortParts()


    $scope.ToggleColor = function(x, oddRowClass, evenRowClass){
        return utility.ToggleRows(x, oddRowClass, evenRowClass);
    }   // CheckParts()


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
