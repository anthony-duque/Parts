
var app = angular.module('LoginApp',[]);

function loginController($scope, $http) {

    $scope.loginError = '';

    $scope.login = function(username, password){

        $http.get('../php/Login.php?user_name=' + username + '&pass_word=' + password)
            .then(function(response) {

                $scope.loginResult = response.data;

                if ($scope.loginResult == 'true'){

                    window.location.href = '../index.html';

                } else {
                    $scope.loginError = $scope.loginResult;
                }
            });
    };  // $scope.login()

}   // loginController()

app.controller('LoginAppController', loginController);