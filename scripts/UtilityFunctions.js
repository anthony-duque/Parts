app.factory('utility', function(){

    var util_Obj = {};

    util_Obj.SortField = function(sortField, prevField){

        var prevOrder = prevField.substring(0,1);

        switch(prevOrder){

          case '+':
             sortOrder = '-';
             break;

          case '-':
             sortOrder = '+';
             break;

          default:
             sortOrder = '+';
             break;

         } // switch()

       return sortOrder + sortField;
    }    // SortField()


    util_Obj.ToggleRows = function(x){

        var bgColor = '';

        if ((x % 2) == 1){
            bgColor = 'white';
        } else {
            bgColor = 'lightBlue';  // temporary until actual status is computed
        }

        return bgColor;
    }   // CheckParts()


    util_Obj.ColorPartStatus = function(partObj){

        var bkgrnd_class = '';  // Background class

        switch (true){

            case (partObj.received_quantity == 0) && (partObj.ordered_quantity == 0) && (partObj.ro_quantity > 0):
                bkgrnd_class = "noParts";
                break;

            case (partObj.received_quantity == partObj.returned_quantity) && (partObj.returned_quantity > 0):
            case (partObj.received_quantity == 0) && ((partObj.ordered_quantity > 0) || (partObj.ro_quantity > 0)):
                bkgrnd_class = "waitingForParts";
                break;

            default:
                bkgrnd_class = "partsComplete";
                break;

        }   // switch()

        return bkgrnd_class;

    }   // ColorPartStatus()


    util_Obj.ColorCarPartsStatus = function(carObj){

        var bgClass = '';
        var pStatus = '';   // part status
        var pWaiting = 0;
        var pUnordered = 0;
        var pReceived = 0;

        carObj.parts.forEach(

            function(carPart, index){
                pStatus = util_Obj.ColorPartStatus(carPart);
                switch(pStatus){

                    case 'noParts':
                        ++pUnordered;
                        break;

                    case 'waitingForParts':
                        ++pWaiting;
                        break;

                    default:
                        ++pReceived;
                        break;
                }
        }); // car.parts.forEach()

        switch(true){

            case (pUnordered > 0) && (pWaiting > 0):
            case (pUnordered > 0) && (pReceived > 0):
                bgClass = 'orange';
                break;

            case (pUnordered > 0) && (pReceived == 0):
                bgClass = 'noParts';
                break;

            case (pWaiting > 0):
                bgClass = 'waitingForParts';
                break;

            default:
                bgClass = 'partsComplete';
                break;

        }   // switch(true)

        return bgClass;

    }   // ColorCarPartsStatus()


    return util_Obj;
});
