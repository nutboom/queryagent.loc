/*
    jqDynaForm

    Author      : (c) 2012 Andrey Tushev (http://tushev.com)
    License     : LGPL (GNU Lesser General Public License http://www.gnu.org/copyleft/lesser.html)
    URL         : http://code.google.com/p/jq-dyna-form/
    Version     : 0.1 alpha
*/
(function ($) {
    $.fn.jqDynaForm = function (method) {
        // Settings
        var addingDeleteText = $(".step-pane quiz__class").hasOwnProperty('0') ? 'задание' : 'вопрос',
            settings = {
            messages: {
                addItemGroup: "Добавить страницу",
                deleteItemGroup: "Удалить страницу",
                addItemQuestion: "Добавить вопрос",
                deleteItemQuestion: "Удалить " + addingDeleteText,
                addItemAnswer: "Добавить ответ",
                deleteItemAnswer: "Удалить ответ",
                addItemImage: "Добавить картинку",
                deleteItemImage: "Удалить картинку",
                deleleItemConfirmation: "Удалить выбранный элемент?",
            }
        };

        var building = false;

        function getSourceItem(itemName) {
            return $("[data-name='" + itemName + "']").not(".jqDynaForm [data-name='" + itemName + "']").first();
        }

        function setFieldValue(field, value) {
            if (value === undefined) {
                return false;
            }

            if (field.attr('type') == 'checkbox') {
                if (value === null || value === undefined || value === '' || value == 0) {
                    field.removeAttr('checked');
                }
                else {
                    field.attr('checked', 'checked');
                }
            } else if (field.attr('type') == 'radio') {
                if (parseInt(value)) {
                    field.attr('checked', 'checked');
                    field.prev().val('0');
                } else {
                    field.removeAttr('checked');
                }
            } else if (field.get(0).tagName.toLowerCase() == 'select') {
                field.children().each(function () {
                    if (this.value == value) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }
            else {
                return field.attr('value', value);
            }
        }

        function setValues(context, json) {
            if (!json) {
                return false;
            }

            // Scan all inputs
            $(':input', context).each(function () {
                // Skip other levels
                if (context.parents('[data-holder-for]').length != $(this).parents('[data-holder-for]').length) {
                    return;
                }

                // Set field value
                var name = $(this).attr('name');
                if (name) {
                    setFieldValue($(this), json[name]);
                }
            });
        }

        function addItem(list, itemName, values) {
            var item = getSourceItem(itemName);
            var newItem = $("<div class='item'></div>").append(item.clone());
            setValues(newItem, values);

            // Delete button
            var titleLinkDelete = '',
                deleteLinkClass = '';
            switch (itemName) {
                case 'groups':
                    titleLinkDelete = settings.messages.deleteItemGroup;
                    deleteLinkClass = 'delete-group';
                    break;
                case 'question':
                    titleLinkDelete = settings.messages.deleteItemQuestion;
                    break;
                case 'answer':
                    titleLinkDelete = settings.messages.deleteItemAnswer;
                    deleteLinkClass = "answer"
                    break;
                case 'picture':
                    titleLinkDelete = settings.messages.deleteItemImage;
                    break;
            }
            $("<div class='delete " + deleteLinkClass + "' title='" + titleLinkDelete + "'>" + titleLinkDelete + "</div>")
                .prependTo(newItem);
            /*.click(function(){
                var item = $(this).parents().filter(':first');
                item.addClass('deleting');
                if(confirm(settings.messages.deleleItemConfirmation)) {
                    item.slideUp(function(){
                        item.remove();
                    });
                }
                else {
                    item.removeClass('deleting');
                }
            });*/

            if (building) {
                list.append(newItem);
            } else {
                newItem.hide();
                list.append(newItem);
                newItem.slideDown();
            }

            switch (itemName) {
                case 'groups':
                    setIndexGroup(list, item, newItem);
                    break;
                case 'question':
                    setIndexQuestion(newItem, item);
                    break;
                case 'answer':
                    setIndexquestionAnswer(newItem, item);
                    break;
                case 'picture':
                    if (values.link) {
                        newItem.find('.pictures_question').removeClass('empty_img');
                        newItem.find('.pictures_question').find('img').attr('src', values.link);
                        newItem.find('.pictures_question').find('img').attr('class', 'img-polaroid');
                        //newItem.find('input[type=file]').val(values.link);
                    }
                    setIndexQuestionImage(newItem, item);
                    break;
                default:
                    break;
            }

            // Init nested holders
            var holders = $("[data-holder-for]", newItem);
            holders.each(function () {
                var nestedName = $(this).attr('data-holder-for');
                initHolder($(this), values[nestedName + 'Array']);
            });
            //$('.Question_type').change();
        }

        function initHolder(holder, array) {
            var itemName = holder.attr('data-holder-for');
            var item = getSourceItem(itemName);

            var header = $("<div class='header'></div>").appendTo(holder);
            var list = $("<div class='list_" + itemName + "'></div>").appendTo(holder);
            var footer = $("<div class='footer'></div>").appendTo(holder);


            var titleLinkAdd = '';
            switch (itemName) {
                case 'groups':
                    titleLinkAdd = settings.messages.addItemGroup;
                    break;
                case 'question':
                    titleLinkAdd = settings.messages.addItemQuestion;
                    break;
                case 'answer':
                    titleLinkAdd = settings.messages.addItemAnswer;
                    break;
                case 'picture':
                    titleLinkAdd = settings.messages.addItemImage;
                    break;
            }
            var addButton = $("<div class='add' title='" + titleLinkAdd + "'></div>").appendTo(footer);
            addButton.click(function () {
                if (itemName == 'groups')
                    doSubmitForm('#structure-quiz-form');
                addItem(list, itemName, {});
            });

            if (array && array.length > 0) {
                for (var n in array) {
                    addItem(list, itemName, array[n]);
                }
            }


            // Moving
            if ($.fn.sortable) {
                var prevPos = 0;
                $(list).sortable({
                    connectWith: "*[data-holder-for='" + holder.attr('data-holder-for') + "'] > .list",
                    start: function (event, ui) {
                        prevPos = ui.item.get(0).offsetTop;
                        ui.item.addClass('moving');
                    },
                    stop: function (event, ui) {
                        ui.item.removeClass('moving');
                        ui.item.parent().find('.conditions').show();
                        ui.item.parent().find('.conditions').first().hide();

                        if (ui.item.get(0).offsetTop != prevPos) {
                            if (itemName == 'groups' && !$(".step-pane").hasClass('mission__class')) {
                                $('#modalRemoteConditions').modal('show');
                                $('#modalRemoteConditions').on('hidden', function () {
                                    $('[data-target].conditions').each(function () {
                                        $($(this).attr('data-target') + ' > .modal-body').html('')
                                    });
                                });
                            }

                            setOrderByForelement(ui.item, itemName);
                        }
                    }
                });
            }
        }



        function setup(doc, json) {
            building = true;

            setValues($(doc), json);

            if (!json) {
                json = {};
            }

            // Init root holders
            var holders = $("[data-holder-for]", doc);
            holders.empty();

            holders.each(function () {
                var name = $(this).attr('data-holder-for') + 'Array';
                initHolder($(this), json[name]);
            });

            // Hovers
            $(".item, .itemsBlock")
                .live('mouseover', function (e) {
                    $(this).addClass('hover');
                    e.stopPropagation();
                })
                .live('mouseout', function (e) {
                    $(this).removeClass('hover');
                    e.stopPropagation();
                });

            building = false;
        }

        function replaceElement(newItem, regword, i) {
            newItem[0].innerHTML = newItem.html().replace(new RegExp(regword, 'g'), i);
        }

        function setIndexGroup(list, item, newItem) {
            var attr_number = item.attr('data-number-group');
            if (attr_number) {
                var i = 0;
                newItem.parents('div#quiz_structure').find('.groups').children().each(function () {
                    var numb = $(this).attr('number')
                    if (numb)
                        if (parseInt(i) < parseInt(numb))
                            i = numb;
                });
                newItem.find('.' + attr_number).html(parseInt(i) + 1);
                newItem.find('fieldset').attr('number', parseInt(i) + 1);
                replaceElement(newItem, attr_number, parseInt(i) + 1);
                return i;
            }
            return 0;
        }

        function getIndexGroup(newItem) {
            var i = newItem.parents('fieldset').last().attr('number');
            return parseInt(i);
        }

        function setIndexQuestion(newItem, item) {
            var attr_number_group = item.attr('data-number-group');
            var group = getIndexGroup(newItem);
            replaceElement(newItem, attr_number_group, group);
            var question = 0;
            newItem.parents('div#quiz_structure').find('.question').find('fieldset').each(function () {
                var numb = $(this).attr('number')
                if (numb)
                    if (parseInt(question) < parseInt(numb)) question = numb;
            });

            newItem.find('fieldset').attr('number', parseInt(question) + 1);
            var attr_number_question = item.attr('data-number-question');
            replaceElement(newItem, attr_number_question, parseInt(question) + 1);
            var g = 0;
                $(".list_groups>.item").each(function() {
                var calc = 1;

                    //var is_anchor_added = false;
                $(this).find(".list_question>.item").each(function() {
                    var question_item = $(this);
                    var anchor = '<a name="g'+g+'q'+(calc-1)+'"></a>';
                    question_item.find("em").find("span").text(calc++);
                    question_item.find("em").find("span").before(anchor);

                    //question_item.find("em").find("span").before(anchor);


//                    var anchor_placement = $(this).find("div[data-number-group='"+group+"'][data-number-question='"+(calc-1)+"']");
//                    console.log("div[data-number-group='"+group+"'][data-number-question='"+(calc-1)+"']");
//                    var anchor = '<a name="g'+(group-1)+'q'+(calc-2)+'"></a>';
//                    anchor_placement.before(anchor);
//                    if (!is_anchor_added){
//                        var anchor = '<a name="g'+(group-1)+'q'+(calc-2)+'"></a>';
//                        $(this).before(anchor);
//                        is_anchor_added = true;
//                    }

                });
                ++g;
            });
        }
        function getIndexQuestion(newItem) {
            var i = newItem.parents('fieldset').first().attr('number');
            return parseInt(i);
        }

        function setIndexquestionAnswer(newItem, item) {
            var attr_number_group = item.attr('data-number-group');
            if (attr_number_group) {
                var group = getIndexGroup(newItem);
                replaceElement(newItem, attr_number_group, group);
            }
            var attr_number_question = item.attr('data-number-question');
            if (attr_number_question) {
                var question = getIndexQuestion(newItem);
                replaceElement(newItem, attr_number_question, question);
            }
            var answ = 0;
            newItem.parents('fieldset').first().find('.answer').each(function () {
                var numb = $(this).attr('number');
                if (numb)
                    if (parseInt(answ) < parseInt(numb)) answ = numb;
            });
            newItem.find('.answer').attr('number', parseInt(answ) + 1);
            var attr_number_question_answer = item.attr('data-number-answer');
            replaceElement(newItem, attr_number_question_answer, parseInt(answ) + 1);

        }

        function setIndexQuestionImage(newItem, item) {
            var attr_number_group = item.attr('data-number-group');
            var group = getIndexGroup(newItem);
            replaceElement(newItem, attr_number_group, group);
            var attr_number_question = item.attr('data-number-question');
            var question = getIndexQuestion(newItem);
            replaceElement(newItem, attr_number_question, question);
            var img = 0;
            newItem.parents('div#quiz_structure').find('.picture').find('input[type=file]').each(function () {
                var numb = $(this).attr('number')
                if (numb)
                    if (parseInt(img) < parseInt(numb)) img = numb;
            });
            newItem.find('input[type=file]').attr('number', parseInt(img) + 1);
            var attr_number_question_img = item.attr('data-number-image');
            replaceElement(newItem, attr_number_question_img, parseInt(img) + 1);
        }

        function doSubmitForm(id) {
            var form = $(id);
            var data = form.serialize();
            $.ajax({
                type: form.attr('method'),
                url: window.location.href,
                data: data,
                success: function (data) {
                    $('#error-message').hide();
                    if (data.errors) {
                        var $ul = '';
                        $.each(data.errors, function (key, val) {
                            $.each(val, function (i, content) {
                                $ul += '<li>' + content.toString() + '</li>';
                            });
                        });
                        $('#error-message').fadeIn("slow");
                        $('#error-message ul').html($ul);
                    }
                    $('#quiz_structure').jqDynaForm('set', data.content);
                    if (!data.errors)
                        $('html, body').animate({
                            scrollTop: $('#quiz_structure  div[data-name=groups]').last().offset().top
                        }, 0);
                },
                error: function (data) { // if error occured
                    /*$("input[type=text]").focus();
                    $('form :input:visible:first').focus();*/
                    $('#structureQuiz').html(data);
                },
                dataType: 'json'
            });
        }

        function setOrderByForelement(item, itemName) {
            var regexp = new RegExp("\w*GroupQuestions_[^\"]*" + itemName + "_orderby", "g");
            var orderby = item.parent().html().match(regexp);
            if (orderby) {
                var dataName = item.find('div[data-name]').attr('data-name');
                var jsonData = {
                    nameOrder: dataName,
                    account: []
                };
                var idEl = ''
                for (var i = 0; i < orderby.length; i++) {
                    $('#' + orderby[i]).val(i + 1);
                    switch (itemName) {
                        case 'groups':
                        case 'question':
                            $('#' + orderby[i]).parents('fieldset').first().attr('number', i + 1);
                            break;
                        case 'answer':
                            break;
                        case 'picture':
                            $('#' + orderby[i]).parent().find('input[type=file]').attr('number', i + 1);
                            break;
                    }
                    idEl = $('#' + orderby[i]).prev().val();
                    jsonData.account.push({
                        id: idEl,
                        orderby: (i + 1)
                    });
                }

                var url = window.location.href + '/setOrderBy';

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: jsonData,
                    dataType: 'json'
                });
            }
        }


        // Methods
        var methods = {
            //
            // Init
            //
            init: function (options) {
                this.addClass('jqDynaForm');
                settings = $.extend(settings, options);
                this.each(function () {
                    setup(this);
                });
            },

            //
            // Get
            //
            get: function () {
                function getFieldValue(field) {
                    if (field.attr('type') == 'checkbox') {
                        return (field.attr('checked') == undefined) ? null : field.attr('value');
                    }
                    else if (field.get(0).tagName == 'select') {
                        return field.val();
                    }
                    else {
                        return field.val();
                    }
                }

                function getItem(context) {
                    var json = {};
                    // Scan all inputs
                    $(':input', context).each(function () {
                        // Skip other levels
                        if (context.parents('[data-holder-for]').length != $(this).parents('[data-holder-for]').length) {
                            return;
                        }

                        var name = $(this).attr('name');
                        if (name) {
                            json[name] = getFieldValue($(this));
                        }
                    });

                    // Scan nested blocks
                    $('[data-holder-for]', context).each(function () {
                        var listName = $(this).attr('data-holder-for') + 'Array';

                        // Scan items within block
                        $('[data-name]', this).each(function () {
                            // Skip other levels
                            if (context.parents('[data-holder-for]').length + 1 != $(this).parents('[data-holder-for]').length) {
                                return;
                            }
                            if (!json[listName]) {
                                json[listName] = [];
                            }
                            json[listName].push(getItem($(this)));
                        });
                    });
                    return json;
                }
                return getItem($(this));
            },

            //
            // Set
            //
            set: function (json) {
                setup(this, json);
            }
        };


        // Method calling logic
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method ' + method + ' does not exist');
        }
    };
})(jQuery);

$(document).ready(function () {
    $(document)
        .on('click', ".btn-remove", function () {
            if (!$(this).attr('disabled')) {
                var elem = $(this).parents('.item').eq(0).find('.answer.delete'),
                    length = $(this).parents('.list_answer').find('.answer.delete').length,
                    $thisParents = $(this).parents(".answers_question"),
                    i = 0;
                elem.trigger('click');
                if (length <= 2) {
                    $(this).parents('.list_answer').find('.btn-remove').filter(function () {
                        return !$(this).parents('.item.hover').hasOwnProperty('0');
                    }).eq(0).attr('disabled', true);
                }
                if (length == 1) {
                    $(this).attr('disabled', true);
                    return false;
                }

                $thisParents.find('.item').not('.hover').each(function() {

                    $(this).find(".number").text(++i + ')');
                });
            }
            return false;
        })
        .on('click', '.btn-add', function () {
            $(this).parents('.answers_question').find('.addAnswer').trigger('click');
            return false;
        })
        .on('click', '.addAnswer', function () {
            var $this = $(this),
                $fieldset = $this.parents('fieldset').eq(0),
                $scale_question = $(document).find('.list_answer').find('.form-group');
                $thisParents = $this.parents(".answers_question"),
                i = 0;
            $thisParents.find('.add').eq(0).trigger('click');
            if ($thisParents.find('.list_answer > .item').length <= 1) {
                $thisParents.find('.btn-remove').attr('disabled', true);
            } else {
                $thisParents.find('.btn-remove').attr('disabled', false);
            }
            if ($fieldset.find('.type_classf_question').find('select').val() == 'scale_close') {
                $fieldset.find('.scale_answer_wrapper').fadeIn(200);
            }
            $thisParents.find('.item').each(function() {
                $(this).find(".number").text(++i + ')');
            });

            return false;
        })
        .on('click', '.addQuestion', function () {
            var $this = $(this).parents('.questionWrapper'),
                i = 0;
            $this.children('div').children('.footer').find('.add').trigger('click');


            $(".list_groups>.item").each(function() {
                var calc = 1;
                $(this).find(".list_question>.item").each(function() {
                    $(this).find("em").find("span").text(calc++);
                });    
            });

            return false;
        })
        .on('click', '.btn-addPage', function () {
            $(this).parents('.step-pane').find(".pageWrapper > .footer").find('.add').trigger('click');
            return false;
        })
        .on('click', '.list_question > .item > .delete', function () {
            $(".list_groups>.item").each(function() {
                var calc = 1;
                $(this).find(".list_question>.item").each(function() {
                    $(this).find("em").find("span").text(calc++);
                });    
            });
        });
});

function addImageButton(element) {
    var $this = $(element);
    $this.parents('.form-group').find('.footer').find('.add').trigger('click');
    console.log($this.parent().find('.images_question').find('.empty_img').eq(0).trigger('click'));
    return;
}


