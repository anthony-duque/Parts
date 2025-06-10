var StageCarCtrlr = function($scope, $http, $window, utility){

    $scope.PlaceInQueue = function(car){

        var foundCarInQueue = false;
        var priority = -1;

            // check if the car is already in the priorityList
        $scope.priorityCars.forEach((eachCar, i) => {
            
            if (eachCar.ro_num == car.ro_num){
                foundCarInQueue = true;
            }
        });     // forEach()

        if(foundCarInQueue){

            alert("Car is already in the queue.");
            return;     // car already in queue.  Just return.

        } else {

                // place the car in queue if not already there
            carIndex = ($scope.priorityCars.push(car) - 1);

            var pCar = {
                priority    : carIndex,
                technician  : car.technician,
                roNum       : car.ro_num,
                locationID  : car.locationID,
                deptCode    : 'BODY'
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
                });
            }   // $http.post()

        }   // PlaceInQueue()

}   // StageCarCtrlr()

app.controller("StageCarController", StageCarCtrlr);
