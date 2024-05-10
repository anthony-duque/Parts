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

    return util_Obj;
});
