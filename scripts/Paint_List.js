var paintListApp = angular.module("PaintListApp", []);

var paintListCtrlr = function($scope, $http){

    $scope.paintList = [];

    GetCarList();

    function GetCarList()
    {
        $http.get('./php/Technician_View.php')
              .then(handleSuccess)
              .catch(handleError);   // .then()
    }

    $scope.AddCarToPaintList = function (car, techIndex, carIndex){

        var carObj = {
            "techIndex": techIndex,
            "carIndex": carIndex,
            "car": car
        };

        $scope.paintList.push(carObj);
        $scope.techList[techIndex].cars.splice(carIndex, 1);
    }

    $scope.DeleteFromPaintList = function(carObj, listIndex){

            // insert back the car to it's original place in the tech list
        $scope.techList[carObj.techIndex].cars.splice(carObj.carIndex, 0, carObj.car);
            // delete from the
        $scope.paintList.splice(listIndex, 1);
    }


/*
    $scope.SavePaintList = function(){

        var carList = [];

        $scope.paintList.foreach(eachCar)=> {
            carList.push(eachCar.car.ro_num);
        }

        $http.post('./php/Paint_List.php', JSON.stringify(carList))
            .then(function(response) {
                     if (response.data){
                        console.log("Paint List written to database!");
                        //alert("Patient Record created!");
                        window.location.href = 'PaintList.html';
                        //console.log(response.data);
                     }
                  },
                  function(response) {
                     console.log("Service does not Exists");
                     console.log(response.status);
                     console.log(response.statusText);
                     console.log(response.headers());
                  });
    }         // SavePaintList()
*/
    function handleSuccess(response)
    {
        if (response.data){
         console.log("Car records fetched successfully!");
         console.log(response.data);
         $scope.techList = response.data;
        }
    }

    function handleError(response)
    {
        console.log("Repair records not fetched.");
    }


}   // paintListCtrlr()

paintListApp.controller("PaintListController", paintListCtrlr);
