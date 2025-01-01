function FilterPartsByDay(parts, numOfDays){

    var filteredParts   = [];
    var date_recvd      = null;
    var curr_date       = new Date();
    var time_diff       = 0;
    var date_diff       = 0;

    if (numOfDays > 1){

        filteredParts = parts;

    } else {

        angular.forEach(parts, function(part){

            date_recvd = new Date(part.invoice_date);
            time_diff = Math.abs(curr_date - date_recvd);
            date_diff = Math.floor(time_diff / (1000 * 60 * 60 * 24));

            if (date_diff == numOfDays){
                filteredParts.push(part);
            }
        });
    }   // if(numDays...)

    return filteredParts;

}   // FilterPartsByDay

app.filter('GetDeliveriesByDay', function(){

        // number of days from today
    return function(partsList, numDays){
        return FilterPartsByDay(partsList, numDays);
    }
});


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
