function ff_init()
{
    $(".ff_list_member").flip({forceHeight: false, forceWidth: false, axis: "y", reverse: true, trigger: "hover", front: ".ff_member_front", back: ".ff_member_back"});
    $("#ff_portrait").flip({forceHeight: false, forceWidth: false, axis: "y", reverse: true, trigger: "hover", front: ".ff_member_front", back: ".ff_member_back"});
    $(".ff_member_back").show();
    $("#fc_search_input").on("input", function(){setTimeout(function(){general_search("/fftracker/search/", "#fc_search_input", ["nav", "#ff_searchlist"])}, search_delay);});
    $("#fc_search_input").on("keypress", function(e){if(e.which == 13) {general_search("/fftracker/search/", "#fc_search_input", ["nav", "#ff_searchlist"])}})
    $("#fc_search_input").focus();
    $("#fc_member_search").on("input", function(){ff_member_search();});
    $("#ff_rank_history").on("toggle", function(){google.charts.setOnLoadCallback(ff_general_chart);});
    $("#ff_ranks_tabs, .fc_rank_perms").tabs({active: 0});
    $("#ff-Register").on("click", ff_register);
    $("#ff_map_zoom").on("click", function(){zoomer($("#ff_map_div"));});
    if (typeof google !== 'undefined') {
        google.charts.setOnLoadCallback(ff_general_chart);
    }
}

function ff_general_chart()
{
    $('[id^=ff_chart_]').each(function() {
        //Process only if visible for some performance optimization
        if ($("#"+this.id).is(':visible')) {
            switch(this.id) {
                case 'ff_chart_ranks':
                    ff_draw_chart(this.id, 'line')
                    break;
                default:
                    ff_draw_chart(this.id, $("#"+this.id).attr('data-type'))
                    break;
            }
        }
    });
}

function ff_draw_chart(chartid, charttype)
{
    var dataarray = JSON.parse($("#"+chartid).attr("data-chart"));
    var data = google.visualization.arrayToDataTable(dataarray);
    //Set common options
    var options = {
        chartArea: {
            backgroundColor: { fill:'transparent' },
            width: '100%', height: '95%'
        },
        backgroundColor: { fill:'transparent' },
        width: '100%',
        height: '100%',
        legend: {
            position: 'right',
            alignment: 'center'
        },
        colors: ['#264c99', '#a52a0d', '#bf7200',
                '#0c7012', '#720072', '#007294',
                '#b72153', '#4c7f00', '#8a2222',
                '#244a6f', '#723372', '#197f72',
                '#7f7f0c', '#4c2699', '#ac5600',
                '#680505', '#4b0c4d', '#256d49',
                '#3f577c', '#2c2e81', '#895619',
                '#10a017', '#8a0e62', '#d30b79',
                '#754227', '#7e930e', '#1f5969',
                '#4c6914', '#8e7b0e', '#084219',
                '#57270c'
        ],
        sliceVisibilityThreshold: 0.0000000001,
        tooltip: {
            showColorCode: true
        }
    };
    //Force custom colors if attribute is set
    if (document.getElementById(chartid).hasAttribute('data-colors')) {
        options.colors = JSON.parse($('#'+chartid).attr('data-colors'));
    }
    if (charttype == 'bar') {
        options.bars = 'horizontal';
        //options.axisTitlesPosition = 'out';
        options.legend = {position: 'none'};
        options.vAxis = {textPosition: 'in', textStyle: {color: '#F3F2F2'}};
        options.hAxis = {textPosition: 'out', textStyle: {color: '#F3F2F2'}};
        options.chartArea.height = '90%';
    }
    if (chartid == 'ff_chart_other_community') {
        //options.isStacked = true;
        options.theme = 'maximized';
        options.legend = {position: 'out'};
        //options.chartArea = {width: '80%', height: '100%'};
    }
    if (chartid == 'ff_chart_character_nameday') {
        options.theme = 'maximized';
        options.hAxis = {textPosition: 'out', maxAlternation: 1, maxTextLines: 5, slantedText: false};
        options.chartArea = {width: '95%', height: '85%'};
        options.legend.position = 'none';
        options.chartArea.top = 0;
    }
    if (chartid == 'ff_chart_freecompany_activity' || chartid == 'ff_chart_other_formed' || chartid == 'ff_chart_other_registered' || chartid == 'ff_chart_other_deleted') {
        options.theme = 'maximized';
        options.legend = {position: 'out', alignment: 'center'};
        options.hAxis = {textPosition: 'none', showTextEvery: 10};
        //options.chartArea = {width: '100%', height: '70%'};
    }
    if (chartid == 'ff_chart_freecompany_activities') {
        options.theme = 'maximized';
        options.legend = {position: 'right', alignment: 'center'};
        options.hAxis = {textPosition: 'none', showTextEvery: 10};
        options.vAxis = {maxValue: 100.00, minValue: 0.00};
        options.chartArea = {width: '80%', height: '100%'};
        options.colors = ['#485fd0', '#487b39', '#813b3c',
                            '#7a5cb9', '#a18f59', '#843302', '#4eaa4e', '#0bc0bf',
                            '#7d3704', '#428a89', '#0b889c', '#b22020', '#9f5653',
                            '#54302c'];
    }
    //Ranking history specific options
    if (chartid == 'ff_chart_ranks') {
        options.chartArea = {width: '100%', height: '80%'};
        options.vAxes = {0: {direction: -1, textPosition: 'none'}, 1: {textPosition: 'none'}};
        options.legend = {position: 'top', alignment: 'center'};
        options.lineWidth = 3;
        options.pointSize = 5;
        options.hAxis = {maxAlternation: 1, maxTextLines: 5, slantedText: false};
        options.series = {0: {targetAxisIndex: 0}, 1: {targetAxisIndex: 0}, 2: {targetAxisIndex: 1}};
    }
    //Create chart object
    switch(charttype) {
        case 'pie':
            var chart = new google.visualization.PieChart(document.getElementById(chartid));
            break;
        case 'line':
            var chart = new google.visualization.LineChart(document.getElementById(chartid));
            break;
        case 'bar':
            var chart = new google.visualization.BarChart(document.getElementById(chartid));
            //var chart = new google.charts.Bar(document.getElementById(chartid));
            break;
        case 'column':
            var chart = new google.visualization.ColumnChart(document.getElementById(chartid));
            break;
    }
    //if (charttype == 'bar') {
        //chart.draw(data, google.charts.Bar.convertOptions(options));
    //} else {
        chart.draw(data, options);
    //}
}

function ff_member_search()
{
    var searchstring = $("#fc_member_search").val().toLowerCase();
    $(".ff_list_member").each(function(index, elem) {
            var entryid = elem.id.toLowerCase();
            var entryname = elem.getAttribute('name').toLowerCase();
            var entryrank = elem.getAttribute('rank').toLowerCase();
            if (entryid.indexOf(searchstring) >= 0 || entryname.indexOf(searchstring) >= 0 || entryrank.indexOf(searchstring) >= 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        }
    );
    $(".ff_list_level").each(function(index, elem) {
            if ($(this).height() <= 15) {
                $(this).hide();
            } else {
                $(this).show();
            }
        }
    );
}

function ff_open_chart(evt, tabname) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("ff_tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("ff_tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(tabname).style.display = "block";
  evt.currentTarget.classList.add("active");
  ff_general_chart();
}

function ff_register()
{
    ajax_ff_reg = true;
    var id = $("#fc_search_input").val();
    ajax_start("Trying to register "+id+"...");
    if (id.match(/^[a-zA-Z0-9]{1,40}$/) != null) {
        $.getJSON('/api/fftracker/register/'+id, 
            function(data) {
                if(['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam'].includes(data)) {
                    ajax_ff_reg = false;
                    window.location.href = '/fftracker/'+data+'/'+id;
                } else {
                    ajax_fail('Failed to register '+id);
                }
            }
        );
        ajax_ff_reg = false;
    } else {
        ajax_fail("Only alphanumerical IDs up to 40 characters are accepted, when trying to register an entity. Please, correct your input.");
    }
}