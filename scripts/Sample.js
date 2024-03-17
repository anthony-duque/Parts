
const ALL_MAKES_LINK = 'https://vpic.nhtsa.dot.gov/api/vehicles/GetAllManufacturers?format=json';

var app = angular.module("sampleModule", [])

//SampleController.$inject('$scope', '$http');
.controller("sampleController", ['$scope', '$http',
    function($scope, $http)
    {
        function processMakes(response)
        {
            if (response.data){
             console.log("Delivery records fetched successfully!");
             console.log(response.data);
             $scope.allMakes = response.data.Results;
            }
        }

        $http.get(ALL_MAKES_LINK)
                .then(processMakes);

        $scope.filterMakes = function(item){
            if(item.Mfr_Name.trim().length > 0){
                return true;
            }else{
                return false;
            }
        }
    }
]);
