var ajax_page_load = false;
var ajax_page_load_err = false;
var ajax_ff_reg = false
var ajax_bic_refresh = false
var defaulthost = location.hostname;
var search_delay = 1500;

$(document).ready(function()
{
    $("#ajaxloadpagestat").flip({forceHeight: false, forceWidth: false, axis: "x", reverse: true, trigger: "manual", front: "#ajaxflipfront", back: "#ajaxloadpageanim"});
    $("#ajaxcog").hide();
    $("#ajaxfail").hide();
    $("#ajaxloadpagestat").flip(true);
    $("#menuicon").on("click", function(){navbarresp("#menuicon");});
    $("#sideicon").on("click", function(){navbarresp("#sideicon");});
    $(".anchor").on("click", function(){anchor(event)});
    $("#sharetog,#contacttog,#rsstog,#sitemaptog").on("change", function(){blocktoggle(event);});
    window.onpopstate = function(event) {
        window.location.href =  document.location;
    };
    //$(".sidebar-content").mCustomScrollbar({axis:"y", theme:"3d-thick", scrollButtons:{enable:true}});
    init();
    $("#ajaxloadpagestat").flip(false);
    $(window).resize(function(){initresize();});
});

function anchor(event)
{
    event.preventDefault();
    var text = new URL(event.srcElement.attributes.href.textContent, document.baseURI).href;
    navigator.clipboard.writeText(text).then(function() {
      alert('Anchor copied to clipboard!');
    }, function(err) {
      console.error('Failed to copy anchor link: ', err);
      alert('Failed to copy anchor link!');
    });
}

function initresize()
{
    if (/fftracker/.test(location) == true && typeof google !== 'undefined') {
         google.charts.setOnLoadCallback(ff_general_chart);
    }
    if ($(".service_wrapper").length) {
        $(".service_wrapper").width($(".service_wrapper>img").width());
        $(".service_wrapper").height($(".service_wrapper>img").width()/1.778);
        $(".service_land").width($(".service_wrapper>img").width() + 4);
        $(".service_wrapper").on("click", shatter);
        if (typeof shatter_paths !== 'undefined') {
            shatter_paths = [];
        }
    }
}

function init()
{
    if (!Modernizr.details) {
        $('details').details();
    }
    $("main").off("wheel");
    $("#autonextwarn").off("touchmove");
    $("main").on("scroll", function(){scrollFunction();});
    $(document).tooltip();
    //$("main").mCustomScrollbar({axis:"yx", theme:"3d-thick", scrollButtons:{enable:true}, callbacks:{whileScrolling:function(){scrollFunction(this.mcs.top, this.mcs.topPct);}}});
    //$("#sidebar, #sidenav").mCustomScrollbar({axis:"y", theme:"3d-thick", scrollButtons:{enable:true}});
    $("#pagination").css({"padding-left": "15px"});
    if (document.hasFocus) {breadcrumbing();}
    if ($("#register-form").length || $("#logout").length) {
        login_init();
    }
    initresize();
    $("#ajaxfail").hide();
    if (/fftracker/.test(location) == true) {
        ff_init();
    } else if (/bic/.test(location) == true) {
        bic_init();
    }
}

function zoomer(element)
{
    element.clone().appendTo("#zoomer");
    //$("#zoomer").mCustomScrollbar({axis:"yx", theme:"3d-thick", scrollButtons:{enable:true}});
    $("#zoomer").show();
    $("#zoomer").on("click", zoomout);
}
function zoomout()
{
    $("#zoomer").empty();
    $("#zoomer").hide();
}

function ajax_start(title)
{
    title = title || '';
    $("#ajaxfail").hide();
    $("#ajaxfail").off("click");
    $("#ajaxcog").show();
    $("#ajaxfail").hide();
    $("#ajaxcog").prop("title", title);
    $("#ajaxloadpagestat").flip(true);
}

function ajax_fail(title)
{
    title = title || '';
    $("#ajaxcog").hide();
    $("#ajaxfail").show();
    $("#ajaxfail").prop("title", title);
    $("#ajaxloadpagestat").flip(true);
}

function ajax_succ()
{
    $("#ajaxcog").hide();
    $("#ajaxfail").hide();
    $("#ajaxloadpagestat").flip(false);
}

function breadcrumbing()
{
    $( ".footprints, .midcat, .finalcat" ).each(function(index){
        var animtime = 500;
        var animdelay = 0;
        if (index != 0) {
            var animdelay = index*animtime;
        }
        if ($(this).hasClass("footprints")) {
            $(this).delay(animdelay).fadeIn(200);
        } else {
            $(this).delay(animdelay).fadeIn(animtime);
        }
    });
}

function navbarresp(element)
{
    if (element == "#menuicon") {
        var togid = "#sidenav";
         $("#sidebar").addClass("hide-on-small");
    } else if (element == "#sideicon") {
        var togid = "#sidebar";
        $("#sidenav").addClass("hide-on-small");
    }
    if (window.matchMedia('(max-width:992px)').matches) {
        if ($(togid).hasClass("hide-on-small")) {
            if (togid == "#sidenav") {
                $(togid).removeClass("hide-on-small").hide().show("slide", { direction: "left" }, 250);
            } else if (togid == "#sidebar") {
                $(togid).removeClass("hide-on-small").hide().show("slide", { direction: "right" }, 250);
            }
        } else {
            if (togid == "#sidenav") {
                $(togid).removeClass("hide-on-small").hide("slide", { direction: "left" }, 250).queue(function(next){
                    $(this).addClass("hide-on-small");
                    next();
                });
            } else if (togid == "#sidebar") {
                $(togid).removeClass("hide-on-small").hide("slide", { direction: "right" }, 250).queue(function(next){
                    $(this).addClass("hide-on-small");
                    next();
                });
            }
        }
    } else {
        $(togid).toggle();
        $(togid).addClass("hide-on-small");
    }
}

function scrollFunction(scrolledto, downpercent)
{
    scrolledto = scrolledto || null;
    downpercent = downpercent || null;
    if ($("#pagination").length) {
        var nextpage = $("#currpage").next("#pagination > span").children().first();
        var margin = ($("#pagination").parent().width() - $("#pagination").width())/2;
        if (scrolledto < -7) {
            $("#pagination").css({"position": "absolute", "top": (Math.abs(scrolledto)+7)+"px", "padding-left": 0});
            if ($(window).height() <= 512) {
                $("#logo, #h1div").hide();
                $("main").css("top", 0);
                $("main").css("height", "calc(100% - 45px)");
            }
        } else {
            $("#pagination").css({"position": "sticky", "top": "7px", "padding-left": "15px"});
            if ($(window).height() <= 512) {
                $("#logo, #h1div").show();
                $("main").css("top", "74px");
                $("main").css("height", "calc(100% - 74px - 45px)");
            }
    }
    }
    if ($("main").scrollTop() > 20 || scrolledto < -20) {
        $(".back-to-top").css("display", "inline-block");
        $("#footer").css("cursor", "pointer");
        if (scrolledto == null) {
            $("#footer").on("click", function(){ $("main").animate({ scrollTop: 0 }, 600)});
        } else {
            //$("#footer").on("click", function(){ $("main").mCustomScrollbar("scrollTo", "top", {scrollInertia:600});});
        }
    } else {
        $("#footer").off("click");
        $(".back-to-top").css("display", "none");
        $("#footer").css("cursor", "");
    }
    hideblocks();
    $("#sharetog").prop("checked", false);
    $("#contacttog").prop("checked", false);
    $("#rsstog").prop("checked", false);
    $("#sitemaptog").prop("checked", false);
    if ($("#pagination").length) {
        if (downpercent == 100 && nextpage.length) {
            if ($("#autonextwarn").css("display") == "none") {
                $("#autonextwarn").show();
                //$("main").mCustomScrollbar("scrollTo","#autonextwarn");
                $("main").on("wheel", function(){window.location.href = nextpage.get(0).href;});
                $("#autonextwarn").on("touchmove", function(){window.location.href = nextpage.get(0).href;});
            }
        }
    }
}

function hideblocks()
{
    $("#sharelist").hide("slide", { direction: "right" }, 500);
    $("#contactme").hide("slide", { direction: "down" }, 500);
    $("#rsslist").hide("slide", { direction: "down" }, 500);
    $("#sitemaplist").hide("slide", { direction: "down" }, 500);
}

function blocktoggle()
{
    hideblocks();
    var togid = "#"+event.target.id;
    if ($(togid).is(':checked')) {
        if (togid == "#sharetog") {
            $("#sharelist").show("slide", { direction: "right" }, 500);
            $("#contacttog").prop("checked", false);
            $("#rsstog").prop("checked", false);
            $("#sitemaptog").prop("checked", false);
        } else if (togid == "#contacttog") {
            $("#contactme").show("slide", { direction: "down" }, 500);
            $("#sharetog").prop("checked", false);
            $("#rsstog").prop("checked", false);
            $("#sitemaptog").prop("checked", false);
        } else if (togid == "#rsstog") {
            $("#rsslist").show("slide", { direction: "down" }, 500);
            $("#sharetog").prop("checked", false);
            $("#contacttog").prop("checked", false);
            $("#sitemaptog").prop("checked", false);
        } else if (togid == "#sitemaptog") {
            $("#sitemaplist").show("slide", { direction: "down" }, 500);
            $("#sharetog").prop("checked", false);
            $("#contacttog").prop("checked", false);
            $("#rsstog").prop("checked", false);
        }
    } else {
        if (togid == "#sharetog") {
            $("#sharelist").hide("slide", { direction: "right" }, 500);
        } else if (togid == "#contacttog") {
            $("#contactme").hide("slide", { direction: "down" }, 500);
        } else if (togid == "#rsstog") {
            $("#rsslist").hide("slide", { direction: "down" }, 500);
        } else if (togid == "#sitemaptog") {
            $("#sitemaplist").hide("slide", { direction: "down" }, 500);
        }
    }
}