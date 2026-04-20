  var app = angular.module('myApp',['ngRoute']);


    app.config(['$routeProvider', '$locationProvider',
        
        function($routeProvider, $locationProvider) {

            $locationProvider.hashPrefix('');

            $routeProvider

                .when('/stage', {
                    'templateUrl': './html/Stage.html'
                })

                .when('/production', {        
                    templateUrl: './html/Production.html'
                })

                .when('/deliveries', {
                    templateUrl: './html/Deliveries.html'
                })

                .when('/parts-search', {
                    templateUrl: './html/Parts_Search.html'
                })

                .when('/materials', {
                        templateUrl: './html/Order_Materials.html'
                })

                .when('/return-forms', {
                    templateUrl: './html/Return_Forms.html'
                })
                
                .when('/follow-up', {
                    templateUrl: './html/Follow_Up.html'
                })

                .when('/vendors', {
                    templateUrl: './html/Vendors.html'
                })

                .otherwise({ redirectTo: '/stage' });
            }   // function()
        ]);     // myApp.config()
    
    app.controller('homeController', function ($scope, $http) {

    });
