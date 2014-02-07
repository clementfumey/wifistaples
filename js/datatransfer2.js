
function reLoadResult(){
    if($('#categoryContent').hasClass('active')){
        if ($(".tab-content.active",'#categoryContent').length != 0){
            var classname = $(".tab-content.active",'#categoryContent').find("table").first().attr('id');
            $("#"+classname).dataTable().fnReloadAjax();
            //alert("ca a peut etre fait");
        }
    }
}

//var timer = setInterval(reLoadResult,20000);

function loadResults(classname)
{
    $(document).ready(function() {
    var isAlready = false;
    $('ul.tab-nav').children().each(function(index){
        $(this).removeClass('active');
        if ($(this).find("a").first().text() == classname){
            $(this).addClass('active');
            isAlready = true;
        }

    });

    $('div.tab-content').removeClass('active');

    $("div.tab-content."+classname).addClass('active');
    var tableContainer = $("#"+classname);
    aoColumns = '[';

    tableContainer.find("th").each(function( index ) {
        if ($(this).attr('stype') != 'undefined'){
            aoColumns += '{"sType":"'+ $(this).attr('stype') +'"}';
        }
        aoColumns += ',';
    });
    aoColumns = aoColumns.slice(0, -1);
    aoColumns += ']';
    alert("aocol : "+aoColumns);
    aoColumns = $.parseJSON(aoColumns);



        tableContainer.dataTable( {
            'aaSorting' : [],
            'aoColumns' : aoColumns,
            'bPaginate' : false,
            'bAutoWidth' : false,
            'bDestroy': true,
            'bServerSide' : false,
            'sAjaxSource': "./result/"+classname+".json",
            'sAjaxDataProp': "aaData",
            // Custom call back for AJAX
            /*fnServerData   : function (sSource, aoData, fnCallback, oSettings) {
                oSettings.jqXHR = $.ajax({
                    dataType: 'json',
                    type    : 'GET',
                    url     : sSource,
                    data    : aoData,
                    success : function (data) {
                        fnCallback(data);
                    }
                });
            },*/
            fnPreDrawCallback: function () {

            },
            fnRowCallback  : function (nRow) {
            },
            fnDrawCallback : function () {
                $('th.no_sort').removeClass('sorting');
            }
        });

        //$("div.toolbar").html('<b>Custom tool bar! Text/images etc.</b>');
        //$("#"+classname).css('width', '');

        addResponsivness(classname);

    });
}

function fnHide( classname, iCol )
{
    /* Get the DataTables object again - this is not a recreation, just a get of the object */
    var oTable = $("#"+classname).dataTable();

    oTable.fnSetColumnVis( iCol, false, true);
    $("#"+classname).css('width', '');
}

function fnShow( classname, iCol )
{
    /* Get the DataTables object again - this is not a recreation, just a get of the object */
    var oTable = $("#"+classname).dataTable();

    oTable.fnSetColumnVis( iCol, true, true);
    $("#"+classname).css('width', '');
}


function addResponsivness(classname)
{



    // add class for scoping styles - cells should be hidden only when JS is on
    $("#"+classname).addClass("enhanced");

    var container = $("<div class=\"table-menu table-menu-hidden\"><ul class=\""+classname+"\"></ul></div>");

    $( "#"+classname+" thead th" ).each(function(i){

       var th = $(this),
          id = th.attr("id"),
          classes = th.attr("class");  // essential, optional, bonus (or other content identifiers)

       // assign an ID to each header, if none is in the markup
       if (!id) {
          id = i;
          th.attr("id", id);
       };

       // create the menu hide/show toggles
       if ( !th.is(".persist") ) {

          // note that each input's value matches the header's ID;
          // later we'll use this value to control the visibility of that header and it's associated cells
          var toggle = $('<li><input type="checkbox" name="toggle-cols" id="toggle-col-'+i+'" value="'+id+'" class="'+classes+'" checked="true"/> <label for="toggle-col-'+i+'">'+th.text()+'</label></li>');

          // append each toggle to the container
          container.find("ul."+classname).append(toggle);

          // assign behavior
          toggle.find("input")

             // when the checkbox is toggled
             .change(function(){
                var input = $(this),
                      val = input.val();  // this equals the header's ID, i.e. "company"


                if (input.is(":checked"))
                {
                    fnShow(classname,val);

                }else {

                    fnHide(classname,val);

                };
             })

             // custom event that sets the checked state for each toggle based on column visibility, which is controlled by @media rules in the CSS
             // called whenever the window is resized or reoriented (mobile)
             /*
             .bind("updateCheck", function(){
                if ( th.css("display") ==  "table-cell") {
                   $(this).attr("checked", true);
                   fnShow(classname,$(this).val());
                }
                else {
                   $(this).attr("checked", false);
                   fnHide(classname,$(this).val());
                };
             })

             // call the custom event on load
             .trigger("updateCheck");*/

       }; // end conditional statement ( !th.is(".persist") )
    }); // end headers loop
/*
    // update the inputs' checked status
    $(window).bind("orientationchange resize", function(){
       container.find("input").trigger("updateCheck");
    });*/

    var menuWrapper = $('<div class="table-menu-wrapper" />'),
       menuBtn = $('<div class="medium default btn"><a href="#" class="table-menu-btn">Display</a></div>');

    menuBtn.click(function(){
       container.toggleClass("table-menu-hidden");
       return false;
    });

    menuWrapper.append(menuBtn).append(container);
     $("#"+classname).before(menuWrapper);  // append the menu immediately before the table

    // assign click-away-to-close event
    $(document).click(function(e){
       if ( !$(e.target).is( container ) && !$(e.target).is( container.find("*") ) ) {
          container.addClass("table-menu-hidden");
       }
    });



    //media query for small screen
    enquire
    .register("screen and (max-width:400px)",{
        match:function(){
            //setColumnvis:false and checked:false for optional
            $("ul."+classname+" input.optional").each( function(){
                val = $(this).val();
                fnHide(classname,val);
                $(this).attr("checked", false);
            });

            $("ul."+classname+" input.bonus").each( function(){
                val = $(this).val();
                fnHide(classname,val);
                $(this).attr("checked", false);
            });
        },
        unmatch : function(){
            //setColumnvis:true and checked:true for optional
            $("ul."+classname+" input.optional").each( function(){
                val = $(this).val();
                fnShow(classname,val);
                $(this).attr("checked", true);
            });
        }
    })
    .register("screen and (min-width: 401px)and (max-width:800px)",{
        match:function(){
            //setColumnvis:true and checked:true for optional
            $("ul."+classname+" input.optional").each( function(){
                val = $(this).val();
                fnShow(classname,val);
                $(this).attr("checked", true);
            });

            //setColumnvis:false and checked:false for optional
            $("ul."+classname+" input.bonus").each( function(){
                val = $(this).val();
                fnHide(classname,val);
                $(this).attr("checked", false);
            });
        },
        unmatch : function(){
            //nothing to do
        }
    })
    .register("screen and (min-width:801px)",{
        match:function(){
            //setColumnvis:true and checked:true for bonus
            $("ul."+classname+" input.bonus").each( function(){
                val = $(this).val();
                fnShow(classname,val);
                $(this).attr("checked", true);
            });
        },
        unmatch : function(){
            //setColumnvis:false and checked:false for optional
            $("ul."+classname+" input.bonus").each( function(){
                val = $(this).val();
                fnHide(classname,val);
                $(this).attr("checked", false);
            });
        }
    });


}

