var StageCarCtrlr = function($scope, $window){

    $scope.DoubleClicked = function(carViewPage){
        $window.open('./html/' + carViewPage, "CarStatus");
    }

}   // StageCarCtrlr()

app.controller("StageCarController", StageCarCtrlr);
