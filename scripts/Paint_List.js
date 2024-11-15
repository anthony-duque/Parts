
var PaintListCtrlr = function($scope, $http){

    GetCarList();
    $scope.paintList = [];
    $scope.statusButton = 'Save List';

    function GetCarList()
    {
        $http.get('./php/Car_List_By_Technician.php')
              .then(
                    function(response){
                        if (response.data){
                            console.log("In-shop car list fetched successfully!");
                            console.log(response.data);
                            $scope.techList = response.data;
                            GetPaintList();
                        }
                    }
              )         // then()
              .catch(

                function(response){
                    console.log("In-shop car list not fetched.");
                }
             );

    }     // GetCarList()

    function GetPaintList()
    {

        var queueList = [];

        $http.get('./php/Paint_List.php')
              .then(
                  function(response)    // onSuccess
                  {
                      if (response.data){

                        console.log("Paint List fetched successfully!");
                        queueList = response.data;
                        console.log(queueList); // test

                        if (queueList.length == 0){

                            $scope.statusButton = "Save List";

                        } else {

                            $scope.statusButton = "Update Status";

                            var car = null;
                            var carIndex = -1;
                            var techIndex = 0;

                            queueList.forEach((qCar) => {    // cycle through all the
                                                                // cars in the paint list
                                $scope.techList.forEach((tech, techIndex) => {
                                                                // find the cars in the in-shop cars
                                    carIndex = tech.cars.findIndex(({ ro_num }) => ro_num == qCar.ro_num);

                                    if (carIndex > -1){
                                        car = tech.cars.find(({ ro_num }) => ro_num == qCar.ro_num);
                                        $scope.AddCarToPaintList(car, techIndex, carIndex, qCar.status);
                                    }
                                });
                            });
                        }
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


    $scope.AddCarToPaintList = function (car, techIndex, carIndex, carStatus){

        var carObj = {
            "car": car,
            "status": carStatus
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
        if ($scope.paintList.length == 0){
            $scope.ClearPaintList();
        }
    }   // DeleteFromPaintList()


    $scope.ClearPaintList = function(){

        while($scope.paintList.length > 0){
            var car = $scope.paintList.shift();
            $scope.DeleteFromPaintList(car, 0);
        }

        $scope.statusButton = 'Save List';
        $scope.SavePaintList();

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

        if ($scope.statusButton == 'Save List'){

                $http.post('./php/Paint_List.php', JSON.stringify(carList))
                    .then(function(response) {
                         if (response.data){
                            console.log(response.data);
                            //console.log("Paint List written to database!");
                            if (carList.length > 0){
                                $scope.statusButton = 'Update Status';
                            } else {
                                $scope.statusButton = 'Save List';
                            }
                         }
                      },
                      function(response) {
                         console.log("Service does not Exists");
                         console.log(response.status);
                         console.log(response.statusText);
                         console.log(response.headers());
                      });


        } else {    // Update each of the car's status

            console.log("Put => " + carList);
            $http.put('./php/Paint_List.php', JSON.stringify(carList))
                .then(function(response) {
                     if (response.data){
                        console.log(response.data);
                        //console.log("Paint List written to database!");
                     }
                  },
                  function(response){
                     console.log("Service does not Exists");
                     console.log(response.status);
                     console.log(response.statusText);
                     console.log(response.headers());
                  });
        }
    }         // SavePaintList()

};   // paintListCtrlr()

app.controller("PaintListController", PaintListCtrlr);
