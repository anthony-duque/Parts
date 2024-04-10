
var app = angular
  .module("prodModule", [])
    .controller("prodController", function($scope, $http){

        $scope.productionView = 'Estimator_View.html';

        $scope.chooseView = function(name){

            switch(name){

                case 'Sep':
                case 'Jim':
                case 'Tony':
                case 'Anthony':
                case 'Chad':
                    $scope.productionView = 'Estimator_View.html';
                    break;

                case 'Van':
                case 'Jose':
                case 'Jerry':
                case 'Nacho':
                case 'Brian':
                    $scope.productionView = 'Technician_View.html';
                    break;

                default:
                    $scope.productionView = 'All_View.html';
                    break;
            }
        }

    });
