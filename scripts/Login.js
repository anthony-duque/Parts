var app = angular.module('LoginApp', []);

function loginController($scope, $http) {

    $scope.x = 10;
    $scope.loginCorrect = true;

    $scope.login = function(username, password) {
            // Perform login logic here
            // For example, validate user credentials and redirect to the dashboard if successful
            // You can use AngularJS services or HTTP requests to handle the login process            
        if (username === 'admin' && password === 'password') {
            $scope.loginCorrect = true;
        } else {
            $scope.loginCorrect = false;
        }
    };
}

app.controller('LoginAppController', loginController);