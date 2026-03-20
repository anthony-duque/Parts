var app = angular.module("NewCustomerApp", []);

var newCustomerCtrl = function($scope, $http){


    $scope.submitNewCustomer = function(newCust){

        switch(true){
        
            case (newCust.passCode !== newCust.passCode_2):
                $scope.return_Message = "Passwords don't match"
                break;

            default:
                $scope.return_Message = "";
                break;
        }
    }
}

app.controller("NewCustomerController", newCustomerCtrl);

