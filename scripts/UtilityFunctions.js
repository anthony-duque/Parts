app.factory('utility', function(){

    var util_Obj = {};

    util_Obj.SortField = function(sortField, prevField){

        var prevOrder = prevField.substring(0,1);

        switch(prevOrder){

          case '+':
             sortOrder = '-';
             break;

          case '-':
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


    util_Obj.ColorPartStatus = function(part){

        var bkgrnd_class = '';  // Background class

        switch (true){

            case (part.received_quantity == 0) && (part.ordered_quantity == 0) && (part.ro_quantity > 0):
                bkgrnd_class = "noParts";
                break;

            case (part.received_quantity == part.returned_quantity) && (part.returned_quantity > 0):
            case (part.received_quantity == 0) && ((part.ordered_quantity > 0) || (part.ro_quantity > 0)):
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

        switch(true){

            case (carObj.parts_unordered > 0) && (carObj.parts_waiting > 0):
            case (carObj.parts_unordered > 0) && (carObj.parts_received > 0):
                bgClass = 'orange';
                break;

            case (carObj.parts_unordered > 0) && (carObj.parts_received == 0):
                bgClass = 'noParts';
                break;

            case (carObj.parts_waiting > 0):
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
