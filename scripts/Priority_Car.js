var PriorityCarCtrlr = function($scope, $http){

    $scope.RemoveFromQueue = function(car){

        var carIndex = -1;

        $scope.priorityCars.forEach((eachCar, i) => {
            if ((eachCar.ro_num == car.ro_num) &&
                (eachCar.locationID == car.locationID)){
                carIndex = i;
            }
        });
            // remove the car from the priority queue
        $scope.priorityCars.splice(carIndex, 1);

        var pCar = {
            technician  : car.technician,
            roNum       : car.ro_num,
            locationID  : car.locationID
        };  // pCar{}

        $http.delete('./php/Tech_Car_Priority.php?ro=' + car.ro_num
                            + '&tech=' + car.technician + '&locID=' + car.locationID)
            .then(
                function(response){
                    if(response.data){
                        console.log("Car RO no. $car.ro_num deleted from the Priority Queue.");
                        console.log(response.data);
                    }
                },
                function(response){
                    console.log("RO no. $car.ro_num was not deleted from the Priority List");
                    console.log(response.status);
                    console.log(response.statusText);
                    console.log(response.headers());
                }
            );
    }   // RemoveFromQueue()


        // Move a car up or down on priority
    $scope.MovePriority = function(car, increment){
        alert("Move Priority");
    }   // MovePriority()

}   // PriorityCarCtrlr()

app.controller("PriorityCarController", PriorityCarCtrlr);
