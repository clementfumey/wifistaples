jQuery.fn.dataTableExt.oSort['mystring-asc'] = function(x,y) {
    var retVal;
    x = $.trim(x);
    y = $.trim(y);

    if (x==y) retVal= 0;
    else if (x == "" || x == "&nbsp;" || x=="DQ") retVal=  1;
    else if (y == "" || y == "&nbsp;" || y=="DQ") retVal=  -1;
    else if (x > y) retVal=  1;
    else retVal = -1;  // <- this was missing in version 1

    return retVal;
}
jQuery.fn.dataTableExt.oSort['mystring-desc'] = function(y,x) {
    var retVal;
    x = $.trim(x);
    y = $.trim(y);

    if (x==y) retVal= 0;
    else if (x == "" || x == "&nbsp;" || x=="DQ") retVal=  -1;
    else if (y == "" || y == "&nbsp;" || y=="DQ") retVal=  1;
    else if (x > y) retVal=  1;
    else retVal = -1; // <- this was missing in version 1

    return retVal;
 }

jQuery.fn.dataTableExt.oSort['numericOrBlank-asc'] = function(x,y) {
    var retVal;
    x = $.trim(x);
    y = $.trim(y);
    if (x=="-" || x===""){
        x=1000;
    }
        if (y=="-" || y===""){
        y=1000;
    }
    retVal = x-y;

    return retVal;
}
jQuery.fn.dataTableExt.oSort['numericOrBlank-desc'] = function(y,x) {
    var retVal;
    x = $.trim(x);
    y = $.trim(y);
    if (x=="-" || x===""){
        x=-1000;
    }
    if (y=="-" || y===""){
        y=-1000;
    }
    retVal = x-y;

    return retVal;
}

