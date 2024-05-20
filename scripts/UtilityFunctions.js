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

            case (partObj.vendor_name.length == 0) && (partObj.part_number.length == 0):
                bkgrnd_class = "partsComplete";
                break;

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
    }

    return util_Obj;
});
