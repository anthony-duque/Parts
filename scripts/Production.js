
var prodController = function($scope, $http, utility){

    $scope.carsInOut    = 'all';
    $scope.sortCars     = '+parts_percent';
    $scope.sortMode     = 'Sort by Parts Received';
    $scope.dueMode      = "What's not Due";

    // default View is Technician View
    $scope.productionView = 'Technician_View.html';

    Get_Technician_List();
    Get_Estimator_List();

//////////////////////////////////////////////////////////


    function Handle_Success(fetchedList){
            console.log(fetchedList + " fetch successful.")

        }

    function Handle_Error(response){
        console.log(response);
    }

    function Get_Technician_List(){

        $http.get('./php/Technician_List.php')  // get all locations by default
              .then(function(response){
                  if(response.data){
                      console.log(response.data);
                      $scope.tech_list = response.data;
                      Handle_Success("Technician List");
                  }
              }).catch(Handle_Error);

    }     // Get_Technician_List()


    function Get_Estimator_List(){

        $http.get('./php/Estimator_List.php')  // get all locations by default
        .then(function(response){
            if(response.data){
                console.log(response.data);
                $scope.estim_list = response.data;
                Handle_Success("Technician List");
            }
        }).catch(Handle_Error);

    }     // Get_Estimator_List()


    $scope.SortCarsDue = function (sortField){

        $scope.sortCars = utility.SortField(sortField, $scope.sortCars);

        var plusMinus = $scope.sortCars.substring(0,1);

        if (plusMinus == '-'){
            $scope.DueButtonClass = "noParts";
            $scope.dueMode = "By what's not due";
        } else {
            $scope.DueButtonClass = "partsComplete";
            $scope.dueMode = "By what's Due";
        }
    }

    $scope.SortCars = function (sortField){

        $scope.sortCars = utility.SortField(sortField, $scope.sortCars);

        var plusMinus = $scope.sortCars.substring(0,1);

        if (plusMinus == '-'){
            $scope.buttonClass = "noParts";
            $scope.sortMode = "What needs Parts";
        } else {
            $scope.buttonClass = "partsComplete";
            $scope.sortMode = "What's Ready";
        }
    }

    $scope.SortCars($scope.sortCars);   //initialize the screen

}   // prodController()

app.controller("prodController", prodController);
