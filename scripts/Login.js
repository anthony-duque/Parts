var app = angular.module('LoginApp', []);

function loginController($scope, $http) {

    $scope.x = 10;
  $scope.login = function() {
    // Perform login logic here
    // For example, validate user credentials and redirect to the dashboard if successful
    // You can use AngularJS services or HTTP requests to handle the login process
  };
}

app.controller('LoginAppController', loginController);