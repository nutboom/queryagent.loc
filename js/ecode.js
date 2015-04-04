$(document).ready(function () {
    var interval;

    if ($("#searchform").val() != '') {
	var val = $("#searchform").val();
	if (val.length >= 2) {
		    $.ajax({
			'type':'POST',
			'dataType':'JSON',
			'data': {
			    'text': val
			    },
			'url': '/site/search/',
			'success': function(response) {
			    $("#searchresults ul").html('');

			    response.forEach(function(e, i, arr){
				var li = $("<li/>"),
				    a = $("<a/>");
				a.attr('href', '/' + e.url).text(e.text).appendTo(li);
				li.appendTo("#searchresults ul");
			    });

			    $("#searchresults").fadeIn(200);
			},
			'error': function(e, m, t) {
			    console.log('error: ' + t);
			}
		    });
	}
    }

//    $('select:not(.chart_type, .Question_type)').each(function () {
//        var options = [],
//        elem = {},
//        selectedItemText = ''
//
//        $(this)
//             .css('display', 'none')
//             .find("option").each(function () {
//                 var optionProperty = {
//                     text: $(this).text(),
//                     value: $(this).val()
//                 };
//                 if ($(this).prop('selected')) {
//                     optionProperty.selected = true;
//                     selectedItemText = $(this).text();
//                 }
//                 options.push(optionProperty);
//             });
//
//        var elem = $('<div class="btn-group"><button type="button" class="btn btn-default">' + selectedItemText + '</button><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu" role="menu"></ul></div>').insertBefore($(this));
//
//        options.forEach(function (e, n, arr) {
//            var elemAppend = elem.find('ul');
//            $('<li><a href="javascript:void(0);">' + e.text + '</a></li>').appendTo(elemAppend);
//        });
//    });

    $('select').each(function(){
        $(this).attr('style','height:35px;');
    })


    function showTooltip(x, y, contents, addClass) {
        var addedClass;
        if (addClass) {
            addedClass = 'class="red"';
        } else {
            addedClass = '';
        };
        $("<div id='tooltip'" + addedClass + ">" + contents + "</div>").css({
            position: "absolute",
            display: "none",
            top: y - 80,
            left: x - 50,
            padding: "5px",
            'color': '#fff',
            'border-radius': '2px',
            'font-size': '11px',
            "width": "100px"
        }).appendTo("body").fadeIn(200);
    }

    function triggerDropdowns(element) {
        var elem = element.parent().parent().next('select'),
            option = elem.find('option'),
            val = option.attr('selected', false).eq(element.index()).attr('selected', true).val();
        elem.val(val);
        elem.trigger('change');
    }


    // ---------------------------------------------------------------

    $(".loadAvatar").on('click', function(){
	$(this).parents('.form-group').find('input[type=file]').trigger('click');
	return false;
    });
    $("#avatar").on('change', function(){
	var file = $(this).val().split('\\').reverse()[0];
	$(".avatarAfter").fadeIn(200).find('.filename').text(file);
    });
    $(document)
	.on('mouseenter', ".question .iCheck-helper", function () {
	    $(this).parent().addClass('hover');
	})
	.on('mouseleave', ".question .iCheck-helper", function () {
	    $(this).parent().removeClass('hover');
	})
	.on('click', ".question .iCheck-helper", function () {
		var parent = $(this).parent();
	    parent.toggleClass('checked');
		parent.find('input[type=hidden]').val(+parent.hasClass('checked'));
    })
	.on('click', '#searchresults li', function(){
		location = $(this).find('a').attr('href');

	})
	.on('click', '.dropdown-menu li', function () {
	    triggerDropdowns($(this));
	})
	.on('mouseover', 'fieldset', function(){
	    return false;
	})

    $("#searchform").on({
        'keydown': function () {
	    clearInterval(interval);
            var val = $(this).val(),
                ajaxData = {
                    text: val
                };
	    if (val != '' && val.length >= 2) {
		interval = setInterval(function(){
		    $.ajax({
			'type':'POST',
			'dataType':'JSON',
			'data': ajaxData,
			'url': '/site/search',
			'success': function(response) {
			    $("#searchresults ul").html('');

			    response.forEach(function(e, i, arr){
				var li = $("<li/>"),
				    a = $("<a/>");
				a.attr('href', e.url).text(e.text).appendTo(li);
				li.appendTo("#searchresults ul");
			    });

			    $("#searchresults").fadeIn(200);
			},
			'error': function(e, m, t) {
			    console.log('error: ' + t);
			}
		    });
		    clearInterval(interval);
		}, 500);
	    } else if (val.length == 1) {
		$("#searchresults").fadeOut(200);
	    }
        },
        'blur': function () {
            $("#searchresults").fadeOut(200);
        },
        'focus': function () {
            if ($(this).val() != '') {
                $("#searchresults").fadeIn(200);
            }
        }
    });

    $(".graph").bind("plothover", function (event, pos, item) {

        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")",
        	addClass;

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                if ($(event.currentTarget).hasClass('mission')) {
                    addClass = 1;
                } else {
                    addClass = 0;
                }
                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1].toFixed(2),
                    d = new Date(x),
                    month = d.getMonth() + 1,
                    html = "<b>Date: </b><span>" + d.getDate() + '.' + month + '.' + d.getFullYear() + "</span></br><b>At all: </b><span>50</span><br/><b>Completed: </b><span>48</span><br/><b>Uncompleted: </b><span>2</span>";
                showTooltip(item.pageX, item.pageY, html, addClass);
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
    $(".question").each(function(n, e) {
        console.log('lol');
    });

    $('.i-element td').not('.nonborder').on({
        'mouseenter': function () {
            $(this).prev('.nonborder').find('b').trigger('mouseenter');
        },
        'mouseleave': function () {
            $(this).prev('.nonborder').find('b').trigger('mouseleave');
        }
    });

    $(window).on('scroll', function(){
	var scrollTop = $(this).scrollTop();

    })

    // ---------------------------------------------------------------

    setTimeout(function () {
        $('select.Question_type').each(function () {
            if (!/\[number_groups\]/.test($(this).attr('name'))) {
                var selectedItem = $(this).find(":selected").index();
                $(this).prev().find('li').eq(selectedItem).trigger('click');
            }
        });

		$('.question .iCheck-helper').each(function(){
			var checked = $(this).parent().find('input[type=checkbox]'),
				parent = $(this).parent(),
				isChecked = checked.prop('checked');
			if (isChecked) {
				parent.addClass('checked');
			}
			parent.append($("<input/>").attr('type', 'hidden').attr('name', checked.attr('name')).val(+isChecked));
		});
		var aQ = $("fieldset").find(".answers_question").find('.item');
		if (aQ.length == 1 && aQ.find('.col-sm-4').find('input').val() == '') {
			aQ.remove();
		}
    }, 100);


    /* -------------------------------------------------------------------- */
    $(".panel-heading").on('click', function() {
    	var quiz = $(this).data("quiz");
    	var showed = $(this).data("showed");

    	if (showed == "0") {
			$.ajax({
				type: "POST",
				url: "/quiz/graph/",
				data: "quiz="+quiz,
				dataType: "json",
				success: function(data) {
					$(this).data("showed", "1");
					var chart;

					// SERIAL CHART
					chart = new AmCharts.AmSerialChart();
					chart.pathToImages = "/js/amcharts/images/";
					chart.dataProvider = data;
					chart.categoryField = "date";
					chart.balloon.bulletSize = 5;

					// AXES
					// category
					var categoryAxis = chart.categoryAxis;
					categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
					categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
					categoryAxis.dashLength = 1;
					categoryAxis.minorGridEnabled = true;
					categoryAxis.position = "top";
					categoryAxis.axisColor = "#DADADA";

					// value
					var valueAxis = new AmCharts.ValueAxis();
					valueAxis.axisAlpha = 0;
					valueAxis.dashLength = 1;
					chart.addValueAxis(valueAxis);

					// GRAPH
					var graph = new AmCharts.AmGraph();
					graph.title = "red line";
					graph.valueField = "value";
					graph.bullet = "round";
					graph.bulletBorderColor = "#FFFFFF";
					graph.bulletBorderThickness = 2;
					graph.bulletBorderAlpha = 1;
					graph.lineThickness = 2;
					graph.lineColor = "#5fb503";
					graph.negativeLineColor = "#efcc26";
					graph.balloonText = "<b>[[value]]</b> анкет";
					graph.hideBulletsCount = 50; // this makes the chart to hide bullets when there are more than 50 series in selection
					chart.addGraph(graph);

					// CURSOR
					chartCursor = new AmCharts.ChartCursor();
					chartCursor.cursorPosition = "mouse";
					chartCursor.pan = true; // set it to fals if you want the cursor to work in "select" mode
					chart.addChartCursor(chartCursor);

					// SCROLLBAR
					var chartScrollbar = new AmCharts.ChartScrollbar();
					chart.addChartScrollbar(chartScrollbar);

					chart.write("plot-"+quiz);
				}
			});
		}

    });
});