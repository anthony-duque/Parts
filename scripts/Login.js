
var app = angular.module('LoginApp',[]);

function loginController($scope, $http) {

    $scope.loginCorrect = true;

    $scope.login = function(username, password){

        $http.get('./php/Login.php?user_name=' + username + '&pass_word=' + password)
            .then(function(response) {
                $scope.loginCorrect = response.data;
                if ($scope.loginCorrect == 'true'){
                    window.location.href = './index.html';
                }
            });
    };  // $scope.login()

}   // loginController()

app.controller('LoginAppController', loginController);