var app = angular.module("carViewModule", []);

var carViewCtrlr = function($scope, $http){

    var params = getQueryParams(window.location.href);

    $scope.sample = params.roNum;


    $scope.car = {
        "ro_num"    : 2594,
        "owner"     : "Mansfield",
        "vehicle"   : "2008 Toyota Tacoma",
        "technician": "Jerry Saucedo",
        "estimator" : "Sep Sadhegi"
    };

    function getQueryParams(url) {

      const queryString = url.split('?')[1];
      const params = {};
      if (queryString) {
        queryString.split('&').forEach((param) => {
          const [key, value] = param.split('=');
          params[key] = decodeURIComponent(value);
        });
      }
      return params;
    }
}

app.controller("carViewController", carViewCtrlr);
