var PriorityCarCtrlr = function($scope){

    $scope.RemoveFromQueue = function(car){

        var carIndex = -1;

        $scope.priorityCars.forEach((eachCar, i) => {
            if ((eachCar.ro_num == car.ro_num) &&
                (eachCar.locationID == car.locationID)){
                carIndex = i;
            }
        });

        $scope.priorityCars.splice(carIndex, 1);
    }   // RemoveFromQueue()


    $scope.MovePriority = function(car, increment){
        alert("Move Priority");
    }   // MovePriority()

}   // PriorityCarCtrlr()

app.controller("PriorityCarController", PriorityCarCtrlr);
