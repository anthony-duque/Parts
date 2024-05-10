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


    util_Obj.CheckPartStatus = function(x, y, percentRcvd){

        var bgColor = '';

        switch(true){

            case (percentRcvd == 1):
                bgColor = 'partsComplete';
                break;

            case (((x + y) % 2) == 1):
                bgColor = 'lightBlue';  // temporary until actual status is computed
                break;

            default:
                bgColor = 'white';
                break;
        }

        return bgColor;

    }   // CheckParts()


    util_Obj.ColorPartStatus = function(ro_qty, ord_qty, rcvd_qty, ret_qty, ven_name, part_num){

        var bkgrnd_class = '';  // Background class

        switch (true) {

            case (ven_name.length == 0) && (part_num.length == 0):
                bkgrnd_class = "partsComplete";
                break;

            case (rcvd_qty == 0) && (ord_qty == 0) && (ro_qty > 0):
                bkgrnd_class = "noParts";
                break;

            case (rcvd_qty == ret_qty) && (ret_qty > 0):
            case (rcvd_qty == 0) && ((ord_qty > 0) || (ro_qty > 0)):
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
