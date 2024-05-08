app.factory('utility', function(){

    var util_Obj = {};

    util_Obj.SortField = function(sortField, prevField){

//        var prevField = oldField.substring(1);
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

    return util_Obj;
});
