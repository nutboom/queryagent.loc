$(document).ready(function() {
    $('#use_group, #use_base').live('click',function(){
    	$("#restrictionForm").show();
    	$("#choose_form").hide();
    	$("#respondents_info").show();
    });


    $('#target-audience-form input, select').live('change',function(){
        var dataes = $('#target-audience-form').serialize();
        var quiz = location.pathname.match(/\/(quiz|mission)\/(.*)\/targetAudience/)[1];
        dataes = 'TargetAudience[quiz_id]='+quiz+'&'+dataes;

        $.ajax({
            dataType: 'json',
            type: 'GET',
            data: dataes,
            url: '/quiz/respondents',
            success: function(data){
                $('#count_respondents').html(data);
            }
        });
    });

    $('.TargetAudience_groups').live('change', function(){
        var checked = $(this).attr('checked');
        if(checked){
        	html = '<li id="'+$(this).attr('id')+'id">'+$(this).next().html()+'</li>';
            $(html).appendTo($(".groups-list ul"));
        } else {
            $("#"+$(this).attr('id')+"id").remove();
        }

        if ($(".groups-list ul").html()) {
        	$("#use_group").attr('disabled', false);
        }
        else {
        	$("#use_group").attr('disabled', true);
        }
    });

    $('.TargetAudience_elements').live('change', function(){
        var checked = $(this).attr('checked');
        if(checked){
            var pEl = $('<ul/>').appendTo($(this).parents('.modal').next().find('.modal-content'));
            pEl.attr('id', $(this).attr('id'));
            pEl.html('<li>'+$(this).next().html()+'</li>');
        } else {
            $(this).parents('.modal').next().find('.modal-content').find('#'+$(this).attr('id')).remove();
        }
    });

    $('.TargetAudience_elements_cities').live('change', function(){
        var checked = $(this).attr('checked');
        if(checked){
            var arrIDCity = $(this).attr('id').split('_');
            var c_id = arrIDCity[arrIDCity.length - 2];
            var couEl = $('ul#'+'TargetAudience_countries_'+c_id);
            var liElem;
            if(couEl.find('span').length > 0){
                liElem = couEl.find('span').parent().first().clone();
                liElem.find('span').attr('id', $(this).attr('id'));
                liElem.find('span').html($(this).next().html()+',&nbsp;');
            } else {
                liElem = couEl.children().first().clone();
                liElem.prepend($('<span />').attr({'id': $(this).attr('id') }).html($(this).next().html()+',&nbsp;'));
                couEl.html('');
            }
            couEl.append(liElem);
        } else {
            var citiElem = $(this).parents('.modal').next().find('.modal-content').find('#'+$(this).attr('id'));
            if(citiElem.parents('ul').children().length > 1)
                citiElem.parent().remove();
            else
                citiElem.remove();
        }
    });

    $('.choose_classfquestion').change(function(){
        var check = (this.checked)?1:0;
        var elemText = $(this).parents('dd').prevAll('dt').html().replace(/^\s+|\s+$/g,'');
        if(check){
            if(!$(this).parents('.modal').next().find('.modal-content').find("[title='"+elemText+"']").get(0)){
                var pEl = $('<ul/>').appendTo($(this).parents('.modal').next().find('.modal-content'));
                pEl.attr('title', elemText);
                pEl.html('<li>'+elemText+'</li>');
            }
        } else {
            if($(this).parents('dd').parents('dl').find('input[type=checkbox]:checked').length == 0)
                $(this).parents('.modal').next().find('.modal-content').find("[title='"+elemText+"']").remove();
        }
    });
    $('.choose_country').change(function(){
        var check = (this.checked)?1:0;
        var arrUrl = $('form').attr('action').split('/');
        var dataes = 'TargetAudience[countries][]='+$(this).val()+'&TargetAudience[checked]='+check+'&audience_id='+parseInt(arrUrl[arrUrl.length - 1])+'';

         $.ajax({
            dataType: 'json',
            type: 'GET',
            data: dataes,
            url: '/catalog/dictCountry/cities',
            success: function(data){
                        $('#city').parent().show();
                        if(!data.text){
                            $('#city #cities_country_'+data.country).html('');
                            if($('#city input[type=checkbox]').length == 0)
                                $('#city').parent().hide();
                        }else
                            $('#city #cities_country_'+data.country).html(data.text);
                    }
          });

          $(this).ajaxComplete(function(){
              if(check){
                if($(this).parents('.modal').next().find('.modal-content').find('#'+$(this).attr('id')).get(0))
                    var pEl = $(this).parents('.modal').next().find('.modal-content').find('#'+$(this).attr('id'))
                else{
                    var pEl = $('<ul/>').appendTo($(this).parents('.modal').next().find('.modal-content'));
                    pEl.attr('id', $(this).attr('id'));
                }
                var arrIDCountry = $(this).attr('id').split('_');
                var c_id = arrIDCountry[arrIDCountry.length - 1];
                var reg = new RegExp('TargetAudience_cities_'+c_id+'_[0-9]+','g');
                var country = $(this).next().html();
                pEl.html('');
                $(this).parents('ul').children().last().find('input').each(function(){
                    if(this.checked) {
                        var per = $(this).attr('id').match(reg);
                        if(per){
                            per.map(function(id, i){
                                pEl.append('<li><span id='+id+'>'+$('#'+id).next().html()+',&nbsp;</span>'+country+'</li>');
                            });
                        }
                    }
                });

                if(!pEl.html())
                    pEl.append('<li>'+country+'</li>');

              } else {
                $(this).parents('.modal').next().find('.modal-content').find('#'+$(this).attr('id')).remove();
              }
          });
    });
    $('#city').parent().hide();
    $('.choose_country,.choose_classfquestion').change();
    $('.choose_country,.choose_classfquestion').change();
});