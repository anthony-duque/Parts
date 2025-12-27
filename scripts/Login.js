var app = angular.module('LoginApp', []);

function loginController($scope, $http) {

    $scope.loginCorrect = true;

    $scope.login = function(username, password){

        if (username === 'admin' && password === 'password') {
            $scope.loginCorrect = true;
        } else {
            $scope.loginCorrect = false;
        }
    };  // $scope.login()

}   // loginController()

app.controller('LoginAppController', loginController);