var sampleApp = angular.module("SampleApp", []);

var sampleCtlr = function($scope, $http){

    GetCars();

    function GetCars(){

        $http.get('./php/Follow_Up.php')  // get all locations by default
              .then(
                    function(response){
                        if (response.data){
                            console.log("Follow up Parts List fetched successfully!");
                            console.log(response.data);
                            $scope.vendors = response.data;
                            GetEstimators();
                        }
                    }
              )         // then()
              .catch(
                    function(response){
                        console.log("Materials list not fetched.");
                    }
             );
    }    // function GetCars()


    function GetEstimators(){

        var estimatorList   = [];
        var estimatorName   = 'eStImAtOr';
        var locationID      = 0;

        const Estimator = {
            "name"   :  "",
            "locID"  :  0
        }

        $scope.vendors.forEach((vendor) => {

            locationID = vendor.locID;

            vendor.cars.forEach((car) => {

                var estimatorFound = false;

                    // is the estimator already in the list?
                estimatorList.forEach((estimator) => {
                    if ((estimator.name == car.estimator) &&
                        (estimator.locID == locationID)){
                        estimatorFound = true;
                    }
                });

                // estimator is not yet in the list
                if (!estimatorFound){
                        // add the estimator to the list
                    var newEstimator = new Object();

                    newEstimator.name   = car.estimator;
                    newEstimator.locID  = locationID;

                    estimatorList.push(newEstimator);
                }   // if(!estimatorList)

            });   //  forEach(car)
        });   // forEach(vendor)

//        console.log(estimatorList);
        $scope.estimators = estimatorList;
    }   // function GetEstimators()

}   // sampleCtlr

sampleApp.controller("SampleController", sampleCtlr);
