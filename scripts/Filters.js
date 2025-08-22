
app.filter("FilterUnassignedCars", function(){

     return function(cars, filterOn){

         var unassignedCars = [];

         if (filterOn){

             cars.forEach(function(eachCar){

                 switch(eachCar.technician){

                     case "":
                     case "BODY":
                     case "IN-HOUSE":
                         unassignedCars.push(eachCar);
                         break;

                     default:
                         break;
                 };   // switch
             });

         } else {

             unassignedCars = cars;
         }

         return unassignedCars;

    } // return function(cars)

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
