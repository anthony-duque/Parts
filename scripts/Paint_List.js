
var PaintListCtrlr = function($scope, $http){

    $scope.paintList = [];

    GetCarList();

    function GetCarList()
    {
        $http.get('./php/Car_List_By_Technician.php')
              .then(handleSuccess)
              .catch(handleError);
    }     // GetCarList()

    $scope.AddCarToPaintList = function (car, techIndex, carIndex){

        var carObj = {
            "car": car,
            "status": 'workNotStarted'
        };

        $scope.paintList.push(carObj);

        $scope.techList[techIndex].cars.splice(carIndex, 1);

    }   // AddCarToPaintList()


    $scope.DeleteFromPaintList = function(carObj, listIndex){

        var techIndex = -1;

        $scope.techList.forEach((eachTech, i) => {
            if (eachTech.technician == carObj.car.technician){
                techIndex = i;
            }
        });

            // Add back to the tech cars
        $scope.techList[techIndex].cars.push(carObj.car);

            // delete from the Paint List
        $scope.paintList.splice(listIndex, 1);

    }   // DeleteFromPaintList()


    $scope.ClearPaintList = function(){

        $scope.paintList.forEach((eachCar, i) => {
            $scope.DeleteFromPaintList(eachCar, i);
        });

    }   // ClearPaintList()


    $scope.ChangeStatus = function(car){

        switch(car.status){

            case 'workNotStarted':
                car.status = 'workUnderway';
                break;

            case 'workUnderway':
                car.status = 'workComplete';
                break;

            case 'workComplete':
                car.status = 'workNotStarted';
                break;
        };
    }   // ChangeStatus()


    $scope.SavePaintList = function(){

        var carList = [];

        $scope.paintList.forEach(

            function (eachCar){
                var car = {
                        "RONum"     : eachCar.ro_num,
                        "Priority"  : eachCar.Priority,
                        "DeptCode"  : 'P',
                        "Status"    : eachCar.status
                };
                carList.push(car);
        });

        console.log("Car List: " + carList);

        $http.post('./php/Paint_List.php', JSON.stringify(carList))
            .then(function(response) {
                     if (response.data){
                        console.log("Paint List written to database!");
                     }
                  },
                  function(response) {
                     console.log("Service does not Exists");
                     console.log(response.status);
                     console.log(response.statusText);
                     console.log(response.headers());
                  });
    }         // SavePaintList()


    function handleSuccess(response)
    {
        if (response.data){
         console.log("Paint List fetched successfully!");
         console.log(response.data);
         $scope.techList = response.data;
        }
    }   // handleSuccess()


    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }

};   // paintListCtrlr()

app.controller("PaintListController", PaintListCtrlr);
