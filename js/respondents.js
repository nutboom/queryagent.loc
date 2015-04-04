$(document).ready(function() {
    /*function cost_sms() {
        var cost = $("#sender_sms").data("cost");
        var balance = $("#sender_sms").data("balance");
        var checked = $("#sender_sms").prop("checked");

        $("#button_pay").hide();

        if (checked) {
            var lines = $("#GroupRespondents_textarea").val().split(";");
            var phones = 0;
            for (var line in lines) {
                var split = lines[line].split(",");
                if (split[1]) {
                    phones++;
                }
            }

            // расчитываем сколько это будет стоить
            var result = Math.ceil( phones * cost );
            // расчитываем сколько денег не хватает
            var needed = balance - result;
            // если денег хватает
            if (needed >= 0) {
                var inform = "Итого "+result+" руб. за рассылку sms-сообщений";
            }
            else {
                var inform = "Итого "+result+" руб. за рассылку sms-сообщений, недостаточно " + (-needed) + " руб.";
                $("#button_pay").show();
            }

            $("#cost_info").show().html(inform);
        }
        else {
            $("#cost_info").hide();
        }   
    }*/
    //функция не нужна, так как нет необходимости в рассылке смс

    /*$("#GroupRespondents_textarea").keyup(function() {
        cost_sms();
    });
    $("#sender_sms").change(function() {
        cost_sms();
    });*/

    /*$("#GroupRespondents_textarea").keyup(function() {

    });*/

	function render_import(data) {
        $("#import_table").html("");
        var header = '\
            <td style="width: 150px;">\
                <select>\
                    <option value="">- не использовать -</option>\
                    <option value="phone">это телефон</option>\
                    <option value="email">это email</option>\
                    <option value="name">это имя</option>\
                    <option value="lastname">это фамилия</option>\
                    <option value="namelastname">это фамилия и имя</option>\
                </select>\
            </td>\
        ';

        // выводим данные
        var html = '';
        var fields = 0; // самое длинное поле
        for (var index in data) {
            var element = data[index];
            var local_fields = 0;

            html += '<tr><td><input type="checkbox" checked="checked"></td>';
            for (var i = 0, count = element.length; i < count; i++) {
                html += '<td>'+element[i]+'</td>';
                local_fields++;
            }
            html += '</tr>';

            // длина самого длинного поля
            fields = (local_fields > fields) ? local_fields : fields;
        }

        // выводим заголовок таблицы
        var caption = '<tr><td><input type="checkbox" checked="checked" id="importchecked" checked></td>';
        caption += new Array(fields+1).join(header);
        caption += '</tr>';

        $("#import_table").append(caption);
        $("#import_table").append(html);

        $("#import_window").show();

        $("#import_window").scrollTop(0);
        $("#import_window").scrollLeft(0);
	}

    $("#importlink").click(function() {
        $("#importfile").trigger("click");

        return false;
    });

    $("#importchecked").live("click", function() {
        var checked = null;
        $("#import_table tr td input").each(function() {
            if (checked == null) {
                checked = $(this).prop("checked");
            }
            else {
                $(this).prop("checked", checked);   
            }
        });
    });

    $("#importcansel").click(function() {
        $("#importbuttons").hide();
        $("#importlink").show();
        $("#import_window").hide();
    });

    $("#importsave").click(function() {
        $("#importbuttons").hide();
        $("#importlink").show();
        $("#import_window").hide();

        var dummy = [], array = [];
        var counter = false; // вылавливаем первую строку таблицы (шапку), чтобы собрать массив с данными о полях
        $("#import_table tr").each(function(element) {
            var checkbox = true;
            $(this).find("td").each(function(index) {
                // это первая строка таблицы
                if (!counter) {
                    var select = $(this).find("select").val();

                    // собираем массив с названияеми сущеностей.
                    dummy[index] = select;
                }
                // а это уже строка с обычными данными
                else {
                    // проверяем, чекбокс ли мы сейчас проверяем
                    if (checkbox) {
                        var checked = $(this).find("input").attr("checked");

                        // если елемент не помечен, то обрабатывать его не надо - выходим из этого уровня цикла
                        if (!checked) {
                            return false;
                        }

                        // обнуляем проверку, т.к. для следующих ячеек этой строки она не нужна
                        checkbox = false;

                        // создаём новый элемент, в который будем помещать данные
                        // смещение на 1, т.к. первый элемент таблицы - его шапка
                        array[element-1] = {};
                    }
                    // нет, это обычное поле с данными
                    else {
                        // если он пометил данное поле как сущность - используем её
                        if (dummy[index]) {
                            var column = ($(this).html() != "null" && $(this).html()) ? $(this).html() : "";

                            // обрабатываем телефон
                            if (dummy[index] == "phone") {
                                column = column.replace(/[^0-9]+/ig, "");
                                column = column.replace(/^8([0-9]+)/ig, "7$1");
                            }
                            array[element-1][dummy[index]] = column; 
                        }   
                    }
                }
            }); 
            counter = true; // переключаем на второую строку таблицы и начинаем обычный разбор данных
        });

        // пробегаем по собранному массиву и вырисовываем данные для вставки в textarea
        var text = "";
        for (var index in array) {
            // если нет фамилии с именем - собираем их
            var namelastname = array[index].namelastname;
            if (!namelastname) {
                if (!array[index].name) array[index].name = "";
                if (!array[index].lastname) array[index].lastname = "";

                namelastname = array[index].lastname + " " + array[index].name;
            }

            if (!array[index].phone) array[index].phone = "";
            if (!array[index].email) array[index].email = "";

            text += namelastname + "," + array[index].phone  + "," + array[index].email + ";\n";
        }

        $("#GroupRespondents_textarea").val($("#GroupRespondents_textarea").val() + text);

        // запускаем формиратор цен
        //cost_sms();

        return false;
    });

    $("#importfile").change(function() {
        var valid = {"txt":1,"csv":1,"xls":1,"xlsx":1};
        var file = $(this)[0].files[0];
        var type = file.name.split(".");
        type = type[type.length - 1];

        if (type in valid) {
            var form = new FormData();
            form.append("file", file);

            $.ajax({
                type: 'POST',
                url: "/import.php",
                data: form,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function() {
                    $("#importloading").show();
                    $("#importlink").hide();
                },
                success: function(data) {
                    render_import(data);

                    $("#importbuttons").show();
                    $("#importloading").hide();
                },
            });
        }
    });
});