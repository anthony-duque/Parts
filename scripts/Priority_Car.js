var PriorityCarCtrlr = function($scope, $http){

        // Move a car up or down on priority
    $scope.MovePriority = function(car, increment){
        alert("Move Priority");
    }   // MovePriority()

}   // PriorityCarCtrlr()

app.controller("PriorityCarController", PriorityCarCtrlr);
