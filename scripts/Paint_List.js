
var PaintListCtrlr = function($scope, $http){

    GetCarList();

    $scope.paintList = [];
//    GetPaintList();

    function GetCarList()
    {
        $http.get('./php/Car_List_By_Technician.php')
              .then(
                    function(response){
                        if (response.data){
                            console.log("In-shop car list fetched successfully!");
                            console.log(response.data);
                            $scope.techList = response.data;
                        }
                    }
              )         // then()
              .catch(
                    function(response)
                    {
                        console.log("In-shop car list not fetched.");
                    }
                );
    }     // GetCarList()


    function GetPaintList()
    {
        $http.get('./php/Paint_List.php')
              .then(
                  function(response)
                  {
                      if (response.data){
                       console.log("Paint List fetched successfully!");
                       console.log(response.data);
                       $scope.carList = response.data;
                      }
                  }   // onSuccess()
              )     // then()
              .catch(
                  function(response)
                  {
                      console.log("Paint List not fetched.");
                  }   // onError()
              );
    }     // GetPaintList()


    $scope.AddCarToPaintList = function (car, techIndex, carIndex){

        var carObj = {
            "car": car,
            "status": 'notStarted'
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

        while($scope.paintList.length > 0){
            var car = $scope.paintList.shift();
            $scope.DeleteFromPaintList(car, 0);
        }
    }   // ClearPaintList()


    $scope.ChangeStatus = function(car){

        switch(car.status){

            case 'notStarted':
                car.status = 'underway';
                break;

            case 'underway':
                car.status = 'completed';
                break;

            case 'completed':
                car.status = 'notStarted';
                break;
        };
    }   // ChangeStatus()


    $scope.SavePaintList = function(){

        var carList = [];

        $scope.paintList.forEach((eachCar, i) => {

            var car = {
                    "RONum"     : eachCar.car.ro_num,
                    "Priority"  : i,
                    "DeptCode"  : 'P',
                    "Status"    : eachCar.status
            };
            carList.push(car);
        });

        console.log("Car List: " + carList);

        $http.post('./php/Paint_List.php', JSON.stringify(carList))
            .then(function(response) {
                     if (response.data){
                        //console.log(response.data);
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

};   // paintListCtrlr()

app.controller("PaintListController", PaintListCtrlr);
