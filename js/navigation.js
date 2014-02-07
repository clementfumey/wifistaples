function navigateTo(divContent)
{
    $(document).ready(function() {
            var divContents = $("#mainContent").children();
            divContents.removeClass('active');
            $("#"+divContent).addClass('active');
    });
}

function tabsTo()
{
    $(document).ready(function() {
            var tabnavs = $(".enhanced-tab-nav").children();
            tabnavs.removeClass('active');
    });
}

