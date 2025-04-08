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


    util_Obj.ToggleRows = function(x, oddRowClass, evenRowClass){

        var bgColor = '';

        if ((x % 2) == 1){
            bgColor = oddRowClass;
        } else {
            bgColor = evenRowClass;
        }

        return bgColor;
    }   // CheckParts()


        // Color codes each part of the car
        // depending on whether:
        //  1)  It has not been ordered (red)
        //  2)  Ordered and not been received. (yellow)
        //  3)  Received (green)
        //  4)  Returned (orange)
    util_Obj.ColorPartStatus = function(part){

        var bkgrnd_class = '';  // Background class

        switch (part.part_status){

                // Part hasn't been ordered
            case "NOT ORDERED":
                bkgrnd_class = "noParts";
                break;

                // Returned part
            case "RETURNED":
                bkgrnd_class = 'lightBlue';
                break;

                // Part ordered but not received
            case "ORDERED":
                bkgrnd_class = "waitingForParts";
                break;

                // part has been received
                // "RECEIVED"
            default:
                bkgrnd_class = "partsComplete";
                break;

        }   // switch()

        return bkgrnd_class;

    }   // ColorPartStatus()


        // Color codes the car in the main production screen
        // depending on the status of the parts
    util_Obj.ColorCarPartsStatus = function(carObj){

        var bgClass = '';

        switch(true){

                // none of the parts have been ordered
            case (carObj.parts_unordered > 0) && (carObj.parts_received == 0):
                bgClass = 'noParts';
                break;

                // There are parts received or ordered but there
                // is at least one part that has not been ordered
            case (carObj.parts_unordered > 0):
                bgClass = 'orange';
                break;

                // All parts ordered but still waiting for parts.
            case (carObj.parts_waiting > 0):
                bgClass = 'waitingForParts';
                break;

                // Parts complete.
            default:
                bgClass = 'partsComplete';
                break;

        }   // switch(true)
        return bgClass;

    }   // ColorCarPartsStatus()


    util_Obj.ColorCarInsurance = function(ins_company){

        var bgClass = '';

        switch(ins_company){

                // none of the parts have been ordered
            case 'Allstate':
                bgClass = 'heading';
                break;

                // There are parts received or ordered but there
                // is at least one part that has not been ordered
            case 'Farmers':
                bgClass = 'orange';
                break;

                // All parts ordered but still waiting for parts.
            case 'State Farm':
                bgClass = 'waitingForParts';
                break;

            case 'AAA':
                bgClass = 'noParts';
                break;

            case 'Geico':
                bgClass = 'partsComplete';
                break;

            case 'Wawanesa':
                bgClass = 'greenOnWhite';
                break;

                // Parts complete.
            default:
                bgClass = '';
                break;

        }   // switch(true)
        return bgClass;

    }   // ColorCarPartsStatus()



    return util_Obj;
});
