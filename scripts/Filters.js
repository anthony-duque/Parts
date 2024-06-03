app.filter('FilterInOutCars', function(){

    return function(cars, inOut){

        var filteredCars = [];
        var date_in = null;
        var curr_date = new Date();

        switch(inOut){

            case 'inShop':

                angular.forEach(cars, function(car){
                    if(car.vehicle_in !== null){
                        date_in = Date.parse(car.vehicle_in);
                        if (date_in < curr_date){
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
