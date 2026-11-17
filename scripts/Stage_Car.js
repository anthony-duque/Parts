var StageCarCtrlr = function($scope, $window, $http){

    $scope.PlaceInQueue = function(car){

        var foundCarInQueue = false;
        var priority = -1;

        if (car == null){

            return;     // no car chosen

        } else {
                // check if the car is already in the priorityList
            $scope.priorityCars.forEach((eachCar, i) => {

                if (eachCar.repair_id == car.repair_id){
                    foundCarInQueue = true;
                }
            });     // forEach()
        }

        if(foundCarInQueue){

            alert("Car is already in the queue.");
            return;     // car already in queue.  Just return.

        } else {

                // place the car in queue
//            carIndex = ($scope.priorityCars.push(car) - 1);
            $scope.priorityCars.push(car);
            queuePriority = $scope.filteredCars.length;   // priority is 1-based, not 0-based

            var pCar = {
                priority    : queuePriority,
                technician  : car.technician,
                repair_id   : car.repair_id,
                dept_code   : 'BODY'
            };  // pCar{}

                // insert car in the database
            $http.post('./php/Tech_Car_Priority.php', JSON.stringify(pCar))

                .then(function(response){
                    if (response.data){
                        console.log(response.data);
                        console.log("Car added to Priority List");
                    }
                },
                function(response){
                    console.log("Car was not added to Priority List");
                    console.log(response.status);
                    console.log(response.statusText);
                    console.log(response.headers());
                });    // $http.post()
            }
        }   // PlaceInQueue()

}   // StageCarCtrlr()

app.controller("StageCarController", StageCarCtrlr);
