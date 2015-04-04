$(document).ready(function () {
    function showTooltip(x, y, contents, addClass) {
        // Функция, которая показывает небольшое всплывающее окно при наведении на точки в графике.
        var addedClass;
        if (addClass) {
            addedClass = 'class="red"';
        } else {
            addedClass = '';
        }
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


    /*
    Кусочек кода, который создает случайные значения для графиков, чтобы все выглядело наглядно.
    */
    /*
    var d = []; 
    for (var i = 0; i <= 11; i++) {
        var sum = 86400 * i;
        d.push([1392076800000 + sum * 1000, Math.round(100 + Math.random() * 100)]);
    }
    */

    $('.graph').each(function () {
        // Сам код, который вставляет значения в графики. Именно он тебе и нужен.
        var colors = $(this).hasClass('mission') ? ["#e36974", "#c64f59", "#52e136"] : ["#2fabdf", "#1f9dcd", "#52e136"]; // Выбираем палитру цветов, в зависимости от текущего графика.
        $.plot($(this), [{
            data: d, // Данные передаются массивом, структура которого такова :
            /*
                [[timestamp, number],[timestamp, number],[timestamp, number]];
                timestamp -  unix timpestamp даты, которая должна быть отображена на оси. Сам таймштамп умножить на 1000!!!!!!!! Это связано с тем, что тут можно задать значения вплоть до мс.
                number - само число, которое должно быть отображено на оси OY. В нашем случае это опросы. Или нет.
                
                Пример: 
                [[1392076800000, 500],[1392163200000, 430],[1392249600000, 551]] создаст график с тремя точками. На оси OX будут даты каких-то чисел февраля, а на OY соответствующие им значения.
            */
            label: "" // Что такое label я не разобрался, так что можно оставить пустым. Или воткнуть наименование того числа, что стоит у нас по оси OY
        }
        ], {
            // Дальше, весь этот большой объект - настройки отображения графика. Их трогать не стоит, потому что все и так работает, как часы.
            series: {
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: {
                        colors: [{
                            opacity: 0.25
                        }, {
                            opacity: 0.25
                        }]
                    }
                },
                points: {
                    show: true
                },
                shadowSize: 2
            },
            legend: {
                show: false
            },
            grid: {
                labelMargin: 10,
                axisMargin: 500,
                hoverable: true,
                clickable: true,
                tickColor: "rgba(0,0,0,0.15)",
                borderWidth: 0
            },
            colors: colors,
            xaxis: {
                mode: "time",
                timeformat: "%d.%m",
                minTickSize: [1, "day"], // Минимальный временной интервал, через который будет отображаться график. "day" || "month" || "year" и так далее. В нашем случае это "day"
                ticks: 100, // Количество точек, которые посчитаются на графике. Если будет передан массив из 11 элементов, например, будет вести себя так, будто значение равно длинне массива.
                tickDecimals: 0
            },
            yaxis: {
                ticks: 5, // Количество пунктов на оси OY
                tickDecimals: 0
            }
        });
    });



    $(".graph").bind("plothover", function (event, pos, item) {
        // Биндим событие на наведение на график. При наведении считаем позицию курсора и выводим туда то, что надо.

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
                    // HTML который отображается в тултипе над точкой. Можно получить только значения с осей. Остальное нужно будет запихивать в глобальный объект и считывать тут.
                    html = "<b>Date: </b><span>" + d.getDate() + '.' + month + '.' + d.getFullYear() + "</span></br><b>At all: </b><span>50</span><br/><b>Completed: </b><span>48</span><br/><b>Uncompleted: </b><span>2</span>"; 
                showTooltip(item.pageX, item.pageY, html, addClass);
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
});