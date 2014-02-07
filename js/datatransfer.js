
function reLoadResult(){
    if($('#categoryContent').hasClass('active')){
        if ($(".enhanced-tab-content.active",'#categoryContent').length != 0){
            var classname = $(".enhanced-tab-content.active",'#categoryContent').find("table").first().attr('id');
            $("#"+classname).dataTable().fnReloadAjax();
            //alert("ca a peut etre fait");
        }
    }
}

var timer = setInterval(reLoadResult,10000);

function loadResults(classname)
{
    $(document).ready(function() {
        var responsiveHelper = undefined;
        var breakpointDefinition = {
            tablet: 800,
            phone : 400
        };
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
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
                }
            },
            fnRowCallback  : function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            fnDrawCallback : function () {
                $('th.no_sort').removeClass('sorting');
                // Respond to windows resize.
                responsiveHelper.respond();
            }
        });

        //$("div.toolbar").html('<b>Custom tool bar! Text/images etc.</b>');
        //$("#"+classname).css('width', '');



    });
}

