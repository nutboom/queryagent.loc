/**
 * Created by alex on 17.02.15.
 */

$(document).on("click", ".get_pdf_statistics", function(event){
    event.preventDefault();
    var svg_arr = '';
    var tempDom = {};

    var input_title = $("<input>").attr("type", "hidden").val($("h2").html());
    input_title.attr("name","title");
    $('#svg-submit-form').append($(input_title));


    $('.actual').each(function(i,ei){
        var actual = $(this)
        var q_text = actual.find(".question_text");
        var highcharts_container = actual.find('.highcharts-container');
        if (highcharts_container){
            highcharts_container.each(function(){
                var svg_code = $(this).html();
                var svg_obj = $.parseHTML(svg_code);
                var tempDom = $('<output>').append(svg_obj);
                tempDom.find('g.highcharts-button').remove();
                var legend = tempDom.find('g.highcharts-legend');
                $("g > g.highcharts-legend-item", legend).children("text").each(function(){
                    var item = $(this);
                    var tspan_count = item.children("tspan").length;
                    if (tspan_count > 0)
                    {
                        for (l=2; l <= tspan_count;++l)
                        {
                            var value = 20+16*(l-1);
                            item.children("tspan:nth-child("+l+")").removeAttr("dy");
                            item.children("tspan:nth-child("+l+")").attr("y",value);
                        }
                    }
                })
                var input_svg = $("<input>").attr("type", "hidden").val(tempDom.html());
                input_svg.attr("name","charts["+i+"][]");
                $('#svg-submit-form').append($(input_svg));
            })
        }
        else{

        }
        var rating = actual.find('.stat-audience-scale-score');

        rating.each(function(){
            var input_rate = $("<input>").attr("type", "hidden").val(rating.html());
            input_rate.attr("name","charts["+i+"][]");
            $('#svg-submit-form').append($(input_rate));
        })

        var input = $("<input>").attr("type", "hidden").val(q_text.html());
        input.attr("name","q_text["+i+"]");
        $('#svg-submit-form').append($(input));

        

    })
    $('#svg-submit-form').submit();
})
