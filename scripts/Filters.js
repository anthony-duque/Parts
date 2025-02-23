app.filter('FilterInOutCars', function(){

    return function(cars, inOut){

        var filteredCars = [];
        var date_in = null;
        var curr_date = Date.parse(new Date());

        switch(inOut){

            case 'inShop':

                angular.forEach(cars, function(car){
                    if(car.vehicle_in !== null){
                        date_in = Date.parse(car.vehicle_in);
                        if ((date_in < curr_date) && (car.current_phase !== '[Scheduled]')){
                            filteredCars.push(car);
                        }
                    }
                });
                break;

            case 'preOrder':

                angular.forEach(cars, function(car){

                    if(car.vehicle_in === null){
                        filteredCars.push(car);
                    }else{
                        date_in = Date.parse(car.vehicle_in);
                        if (date_in > curr_date){   // vehicle in in the future
                            filteredCars.push(car);
                        }
                    }
                });
                break;

            default:
                filteredCars = cars;
                break;
        }

        return filteredCars;
    }   // function(cars, inOut)
});


app.filter('FilterCarsByLocation', function(){

    return function(cars, locationID){

        var filteredCars = [];

        if(locationID == 0){
            filteredCars = cars;
        } else {
            angular.forEach(cars,
                function(car){
                    if(car.loc_ID == locationID){
                        filteredCars.push(car);
                    }
                }   // function(car)
            );
        }   // if-else

        return filteredCars;
    }   // function(cars, locationID)
});


app.filter('FilterByLocation', function(){

     // assignees can be tech of estimator
    return function(assignedCars, locationID){

        var filteredCars = [];

        if(locationID == 0){

            filteredCars = assignedCars;

        } else {

            assignedCars.forEach(

                function(assignee){

                    var cars = assignee.cars;
                    var allCarsInLocation = true;

                    for (var i = 0; i < cars.length; ++i){
                        if (cars[i].loc_ID != locationID){
                            allCarsInLocation = false;
                            break;
                        }   // if()
                    }   // for(var)

                    if (allCarsInLocation){
                        filteredCars.push(assignee);
                    }
                }
            );
        }   // if-else

        return filteredCars;
    }   // function(cars, locationID)
});
