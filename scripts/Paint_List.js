
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
            "techIndex": techIndex,
            "carIndex": carIndex,
            "car": car,
            "status": 'workNotStarted'
        };

        $scope.paintList.push(carObj);
        $scope.techList[techIndex].cars.splice(carIndex, 1);

    }   // AddCarToPaintList()


    $scope.DeleteFromPaintList = function(carObj, listIndex){

            // insert back the car to it's original place in the tech list
        $scope.techList[carObj.techIndex].cars.splice(carObj.carIndex, 0, carObj.car);
            // delete from the
        $scope.paintList.splice(listIndex, 1);
    }   // DeleteFromPaintList()


    $scope.ClearPaintList = function(){

        while($scope.paintList.length > 0){
            var carObj = $scope.paintList.shift();
            $scope.techList[carObj.techIndex].cars.splice(carObj.carIndex, 0, carObj.car);
        }
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

            function BuildCarList(eachCar, index){
                var car = {
                        "RONum"     : eachCar.car.ro_num,
                        "Priority"  : index,
                        "TechIndex" : eachCar.techIndex,
                        "CarIndex"  : eachCar.carIndex,
                        "Status"    : eachCar.status
                };
                carList.push(car);
        });

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
