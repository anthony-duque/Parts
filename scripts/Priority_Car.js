var PriorityCarCtrlr = function($scope, $http){

        // Move a car up or down on priority
    $scope.MovePriority = function(curr_priority, increment){
        
        if (curr_priority + increment < 0 || curr_priority + increment >= $scope.filteredCars.length) {

            console.log("Error: Cannot move car at index " + curr_priority + " by increment " + increment);

        } else{

            const carToMoveID = $scope.filteredCars[curr_priority].repair_id;
            const bumpedCarID = $scope.filteredCars[curr_priority + increment].repair_id;

            $http.put('./php/Tech_Car_Priority.php?carToMoveID=' + carToMoveID + 
                            '&incr=' + increment + '&bumpedCarID=' + bumpedCarID)
                .then(function(response){

                    if (response.status === 200) {

                        console.log("Successfully moved car at index " + curr_priority + " by increment " + increment);
                            // swap the two cars in the filteredCars array
                        const carToMoveIndex = $scope.priorityCars.findIndex(car => car.repair_id === carToMoveID);
                        const bumpedCarIndex = $scope.priorityCars.findIndex(car => car.repair_id === bumpedCarID);

                        if (carToMoveIndex !== -1 && bumpedCarIndex !== -1) {
                            const temp = $scope.priorityCars[carToMoveIndex];
                            $scope.priorityCars[carToMoveIndex] = $scope.priorityCars[bumpedCarIndex];
                            $scope.priorityCars[bumpedCarIndex] = temp;
                        }
                    } else {
                        console.log("Error: Failed to move car at index " + curr_priority + " by increment " + increment);
                    }
                })
                .catch(function(error){
                    console.log(error);
                });
            }
    }   // MovePriority()

}   // PriorityCarCtrlr()

app.controller("PriorityCarController", PriorityCarCtrlr);
