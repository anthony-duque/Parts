
var app = angular.module('LoginApp',[]);

function loginController($scope, $http, $window) {

    $scope.loginError = '';

    $scope.login = function(username, password){

        $http.get('../php/Login.php?user_name=' + username + '&pass_word=' + password)
            .then(function(response) {

                $scope.loginResult = response.data;

                if ($scope.loginResult.success === true){   // company has an account

                    $window.sessionStorage.setItem('companyID', $scope.loginResult.companyID);   // store companyID in session storage for use in other pages

                    if ($scope.loginResult.locationIDs.length > 0) {   // company has shops associated with the account

                            // store locationIDs in session storage for use in other pages
//                        $window.sessionStorage.setItem('locationIDs', 
//                                $scope.loginResult.locationIDs);   // convert array of location IDs to comma-separated string
                        
                        $window.location.href = '../index.html';

                    } else {    // company has no shops associated with the account

                        alert("Login successful, but no shops are associated with this account. Please upload CSV extracts.");
                        $window.location.href = './admin/Admin.html';

                    }

                } else {    // login failed ($scope.loginResult.success === false)

                    $scope.loginError = $scope.loginResult.message;
                }
            });
    };  // $scope.login()

}   // loginController()

app.controller(
    'LoginAppController', 
    [ 
        '$scope', '$http', '$window', loginController
    ]
);