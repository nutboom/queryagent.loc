$(document).ready(function () {
    initStruct();
});

    function initStruct(){
        $('#error-message').fadeOut(200);
        $('#quiz_structure').jqDynaForm();

        $('.Question_type').live('change', function () {
            $(this).parents('fieldset').first().find('.scale_question').fadeOut(200);
            $(this).parents('fieldset').first().find('.answers_question').find('.scale_answer_wrapper').fadeOut(200);
            $('#subforms_library .scale_answer_question').fadeOut(200);
            $(this).parent().find('input[type=checkbox]').prop('disabled', true);
            $('#quiz_structure .list_groups .conditions').first().fadeOut(200);

            switch ($(this).val()) {
                case 'open':
                    $(this).parents('fieldset').first().find('.answers_question').fadeOut(200);
                    break;
                case 'close':
                    $(this).parents('fieldset').first().find('.answers_question').fadeIn(200);
                    $(this).parent().find('input[type=checkbox]').removeAttr('disabled');
                    break;
                case 'semiclose':
                    $(this).parents('fieldset').first().find('.answers_question').fadeIn(200);
                    break;
                case 'scale_close':
                    $(this).parents('fieldset').first().find('.answers_question').fadeIn(200);
                    $(this).parents('fieldset').first().find('.scale_answer_wrapper').fadeIn(200);
                    $('#subforms_library .scale_answer_question').fadeIn(200);
                    break;
                case 'scale_score':
                    $(this).parents('fieldset').first().find('.answers_question').fadeOut(200);
                    $(this).parents('fieldset').first().find('.scale_question').fadeIn(200);
                    break;
                case 'close_multiple_choice':
                    $(this).parents('fieldset').first().find('.answers_question').fadeIn(200);
                    break;
                case 'answer_photo':
                    $(this).parents('fieldset').first().find('.answers_question').fadeOut(200);
                    break;
            }
        });

        $('.pictures_question').live('click',function(){
                $(this).parent().find('input[type=file]').click();
        });

        $('.picture_file').live('change',function() {
            var FileImg = $(this).val();
            var name = $(this).attr('name');
            $.ajaxFileUpload(
                {
                    url:window.location.href+'/saveImg',
                    secureuri:false,
                    fileElementId:$(this).attr('id'),
                    dataType: 'json',
                    success: function (data, status)
                    {
                        if(data.msg){
                            $('#'+data.id).parent().find('.pictures_question').html('');
                            $('#'+data.linkId).val(data.name);
                            var img = $('<img />').attr({'src': data.msg, 'alt':data.id }).appendTo($('#'+data.id).parent().find('.pictures_question'));
                            img.addClass('img-polaroid');
                            $('#'+data.id).parent().find('.pictures_question').removeClass('empty_img').delay(3000);
                        }
                    },
                    error: function (data, status, e)
                    {
                        alert(e);
                    }
                }
            );
        });

        $('.delete').live('click',function(){
            var item = $(this).parents().filter(':first');
            var imgID = item.find('input[type=hidden]').val();
            var dataName = item.find('div[data-name]').attr('data-name');
            var src = '';
            if(dataName == 'picture'){
                src = item.find('img').attr('src');
            }
            $.ajax({
                type: 'POST',
                data: {dataName: dataName, id: imgID, src: src},
                url: window.location.href+'/deleteElement',
                dataType: 'json'
            });
            item.slideUp(function(){
                var indexItem = item.index();
                var parentItem = item.parent();
                item.remove();        
                if(dataName == 'groups'){
                    if(indexItem == 0){
                        parentItem.find('.conditions').show();
                        parentItem.find('.conditions').first().hide();
                    }
                }
            });
            var i = 0;
            item.parent().find('.item:not(.hover)').each(function(){
               $(this).find('.number').text(++i + ')');
            });
            i = null;
        });

        $('.conditions').live('click',function(){
            var groupOrder = $(this).parents('fieldset').find('input[type=hidden]').get(1).value;
            if(!$($(this).attr('data-target')+' > .modal-body').html()){
                $.ajax({
                    type: 'GET',
                    url: window.location.href+'/conditionsDisplay/'+groupOrder,
                    success:function(data){
                        var arrUtl = this.url.split('/');
                        var fieldsetEl;
                        $('#quiz_structure  div[data-name=groups] > fieldset').each(function(){
                            if(parseInt($(this).attr('number')) == parseInt(arrUtl[arrUtl.length - 1]))
                                fieldsetEl = $(this);
                        });
                        if(fieldsetEl){
                            var idEl = '#' + fieldsetEl.find('.modal').attr('id');
                            var arrID = idEl.split('_');
                        }
                        var regexp = new RegExp('\w*GroupQuestions(.?)'+arrUtl[arrUtl.length - 1],'g');
                        var textInput = data.responseText.replace(regexp, 'GroupQuestions$1'+arrID[arrID.length - 1]);
                        if(idEl && (!$(idEl).find('.modal-body').html() || parseInt(arrUtl[arrUtl.length - 1]) != parseInt(arrID[arrID.length - 1])))
                            $(idEl).find('.modal-body').html(textInput);
                    },
                    error: function(data) { // if error occured
                        var arrUtl = this.url.split('/');
                        var fieldsetEl;
                        $('#quiz_structure  div[data-name=groups] > fieldset').each(function(){
                            if(parseInt($(this).attr('number')) == parseInt(arrUtl[arrUtl.length - 1]))
                                fieldsetEl = $(this);
                        });
                        if(fieldsetEl){
                            var idEl = '#' + fieldsetEl.find('.modal').attr('id');
                            var arrID = idEl.split('_');
                        }
                        var regexp = new RegExp('\w*GroupQuestions(.?)'+arrUtl[arrUtl.length - 1],'g');
                        var textInput = data.responseText.replace(regexp, 'GroupQuestions$1'+arrID[arrID.length - 1]);
                        if(idEl && $(idEl).find('.modal-body').html().length != textInput.length)
                            $(idEl).find('.modal-body').html(textInput);
                    },
                    dataType: 'json'
                });
            }
        });

        $('.answers_condition').live('change',function() {
            var isCheckedHere = $(this).attr('checked');
            if (!isCheckedHere)
                $(this).parent().children().each(function(){
                    if($(this).attr('checked'))
                        isCheckedHere = $(this).attr('checked')
                });
            if (isCheckedHere) {
                $(this).parents('.form.well > ul').children().not($(this).parents('li').last()).fadeOut('slow');
                $(this).parents('.form.well > ul ul').children().not($(this).parents('li').first()).fadeOut('slow');
            } else {
                $(this).parents('.form.well > ul').children().not($(this).parents('li').last()).fadeIn('slow');
                $(this).parents('.form.well > ul ul').children().not($(this).parents('li').first()).fadeIn('slow');
            }
        });
    };












function setPreView(data) {
    var questions = 0;
    // генерируем список
    html = '<div class="preview_questions">';
    for (var gindex in data.groupsArray) {
        html += '<div class="group_question"><div class="group">Блок вопросов '+(parseInt(gindex)+1)+'</div>';
        for (var qindex in data.groupsArray[gindex].questionArray) {
            // запускаем рисовалку
            var question  = data.groupsArray[gindex].questionArray[qindex]['GroupQuestions[number_groups][Question][number_question][text]'];
            var type  = data.groupsArray[gindex].questionArray[qindex]['GroupQuestions[number_groups][Question][number_question][type]'];
            var scaled_size  = data.groupsArray[gindex].questionArray[qindex]['GroupQuestions[number_groups][Question][number_question][scaled_size]'];
            var answers = [];
            var pictures = [];

            for (var aindex in data.groupsArray[gindex].questionArray[qindex].answerArray) {
                answers.push(data.groupsArray[gindex].questionArray[qindex].answerArray[aindex]['GroupQuestions[number_groups][Question][number_question][answer][number_answer][text]']);
            }

            for (var pindex in data.groupsArray[gindex].questionArray[qindex].pictureArray) {
                pictures.push(data.groupsArray[gindex].questionArray[qindex].pictureArray[pindex]['link']);
            }

            if (question) {
                html += qustion_html(question, type, scaled_size, answers, pictures);
                questions++;
            }
        }
        html += '</div>';
    }

    html += '</div>';

    if (questions) {
        $("#preview_quiz").html(html);
    }
    else {
        $("#preview_quiz").html("Заполните анкету");
    }
}

function qustion_html(question, type, scaled_size, answers, pictures) {
    var html = '<div class="block_question"><div class="question">'+question+'</div>';

    // ответы
    html += '<div class="answers">';
    for (var index in answers) {
        html += '<div>'+(parseInt(index)+1)+') '+answers[index]+'</div>';
    }
    html += '</div>';

    // фотографии
    html    +=  '<div class="pictures">';
    for (var index in pictures) {
        html    +=  '<img src="'+pictures[index]+'">';
    }
    html    +=  '</div>';


    html    +=  '</div>';
    return html;
}