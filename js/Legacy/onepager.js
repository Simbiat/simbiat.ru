function onepager(event, href, elems_to_upd)
{
    href = href || null;
    elems_to_upd = elems_to_upd || ["title", "h1", "nav", "main", "meta", "link", "#usercpblock"];
    if (event == null) {
        if ((window.location.href != href && ajax_page_load == false) || ajax_page_load_err == true) {
            ajax_page_load = true;
            onepager_ajax(href, elems_to_upd);
            history.pushState(href, $("title").html(), href);
        }
    } else if (event == "navigation") {
        if (location.hostname == defaulthost) {
            onepager_ajax(href, elems_to_upd);
        }
    } else {
        var link = $(event.target);
        if (!link.is("a")) {
            link = link.parents("a");
        }
        hostcheck = new RegExp(location.protocol+"//"+location.host+"/");
        if (link.get(0).target != "_blank" && link.get(0).target != "_top" && /#/.test(link.get(0)) == false && /javascript/.test(link.get(0)) == false && link.get(0).toString().match(hostcheck) != null) {
            event.preventDefault();
            var url = link.get(0).href;
            if (((window.location.href != url || link.hasClass("postlink")) && ajax_page_load == false) || ajax_page_load_err == true) {
                ajax_page_load = true;
                if (link.hasClass("postlink")) {
                    onepager_ajax(url, elems_to_upd, link.data());
                } else {
                    onepager_ajax(url, elems_to_upd);
                }
                history.pushState(url, $("title").html(), url);
            }
        }
    }
}

function general_search(urlbase, searchelem, elems_to_upd) {
    var initsearch = encodeURIComponent($(searchelem).val());
    if (initsearch.length == "") {
        window.location.href = urlbase;
    } else {
        window.location.href = urlbase+initsearch+"/";
    }
    if (decodeURIComponent(location.href.toString()).match(new RegExp(urlbase+$(searchelem).val()+"/")) == null) {
        window.location.href = urlbase+$(searchelem).val()+"/";
    }
}

function onepager_social_share(url, title)
{
    if (typeof FB !== 'undefined' && typeof FB.XFBML !== 'undefined') {
        $(".fb-share-button").attr("data-href", url).removeAttr("fb-xfbml-state").removeAttr("fb-iframe-plugin-query").removeClass("fb_iframe_widget").empty();
        FB.XFBML.parse();
    }
    $("[id^=vkshare] a").attr("href", "https://vk.com/share.php?url="+encodeURIComponent(url));
}

function onepager_ajax(url, elems_to_upd, postdata)
{
    var method = "GET";
    var processData = false;
    postdata = postdata || null;
    if (postdata) {
        method = "POST";
        processData = true;
    }
    $("main").mCustomScrollbar("scrollTo", "top", {scrollInertia:10});
    ajax_start("Trying to load page "+url+"...");
    $("#ajaxfail").on("click", function(url){onepager(null, url);});
    var title = $("title").html();
    $.ajax({
        headers: {
            'X-Csrftoken': $('meta[name="X-Csrftoken"]').attr('content')
        },
        "url": url+"?onepager",
        "method": method,
        "processData": processData,
        "data": postdata,
        "dataType": "html",
        "elems_to_upd": elems_to_upd,
        "success": function(data, response, jqXHR){onepager_update(data, response, jqXHR, this.elems_to_upd);},
        "complete": function(event, xhr){onepager_history(event, xhr, this.url.replace("?onepager", ""));},
    });
}

function onepager_update(data, response, jqXHR, elems_to_upd)
{
    var newdata = $("<output>").append($.parseHTML(data));
    $("main, #sidebar, #sidenav").mCustomScrollbar("destroy");
    $.each(elems_to_upd, function(index, elem) {
        if (elem == "h1") {
            if (document.hasFocus) {
                $("h1").effect("bounce").html($("h1", newdata).html().replace(/<h1.*>(.*)<\/h1>/g, "$1"));
            } else {
                $("h1").html($("h1", newdata).html().replace(/<h1.*>(.*)<\/h1>/g, "$1"));
            }
        } else if (elem == "nav") {
            if (document.hasFocus) {
                $("nav ol").hide("explode", {"pieces": 16}, "slow");
            }
            $("nav").html($("nav", newdata).html());
        } else if (elem == "main") {
            if (document.hasFocus) {
                $("main").effect("shake", {"distance": 1}).html($("main", newdata).html());
            } else {
                $("main").html($("main", newdata).html());
            }
        } else if (elem == "meta") {
            $.each($("meta", newdata), function(metaindex, metaelem) {
                if (metaelem.name.length) {
                    $('meta[name="'+metaelem.name+'"]').attr('content', metaelem.content);
                } else {
                    if (metaelem.getAttribute('property') != null && metaelem.getAttribute('property').length) {
                        $('meta[property="'+metaelem.getAttribute('property')+'"]').attr('content', metaelem.content);
                    } else {
                        if (metaelem.getAttribute('itemprop') != null && metaelem.getAttribute('itemprop').length) {
                            $('meta[itemprop="'+metaelem.getAttribute('itemprop')+'"]').attr('content', metaelem.content);
                        }
                    }
                }
            });
        } else if (elem == "link") {
            $.each($('link[rel="alternate"]'), function(linkindex, linkelem) {
                $(linkelem).remove();
            });
            $.each($('link[rel="alternate"]', newdata), function(linkindex, linkelem) {
                $('head').append($(linkelem, newdata));
            });
        } else {
            $(elem).html($(elem, newdata).html());
        }
    });
    if (window.matchMedia('(max-width:992px)').matches) {
        if (!$("#sidebar").hasClass("hide-on-small")) {
            $("#sidebar").addClass("hide-on-small");
        }
        if (!$("#sidenav").hasClass("hide-on-small")) {
            $("#sidenav").addClass("hide-on-small");
        }
    }
    onepager_social_share(window.location.href, $("title").text());
}

function onepager_history(event, response, url)
{
    if (response == "success") {
        init();
        ajax_succ();
    } else {
        ajax_page_load_err = true;
        ajax_fail("Failed to load page. Click on me to try again");
    }
    ajax_page_load = false;
}