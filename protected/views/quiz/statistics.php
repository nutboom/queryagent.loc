<?php
Yii::app()->getClientScript()->registerCssFile('/css/app_stat.css');
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app','Statistics '.$model->type);
$this->breadcrumbs=array(
    Yii::t('app', ucfirst($model->type).'s')=>array('/'.$model->type),
    Yii::t('app', 'Statistics '.$model->type),
);
$this->menu=array(
    array('label'=>Yii::t('app', 'Export in .XLS'),'url'=>array('/quiz/'.$id.'/excel')),
    array('label'=>Yii::t('app', 'Export in .CSV'),'url'=>array('/quiz/'.$id.'/export')),
    array('label'=>Yii::t('app', 'Export in .PDF'),'url'=>array('/quiz/'.$id.'/statistics#'),'linkOptions'=>array('class'=>'get_pdf_statistics'),),
);
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/js/js_highcharts/js/highcharts.js"></script>
    <script src="/js/js_highcharts/js/highcharts-3d.js"></script>
    <script src="/js/js_highcharts/js/modules/exporting-with-html.src.js"></script>
<? if(!isset($_GET['theme'])||$_GET['theme']=='grid_light'){?>
    <script src="/js/highcharts/themes/grid-light.js"></script>
    <?}
elseif(isset($_GET['theme']) && $_GET['theme']=='sand_signika'){?>
    <script src="/js/highcharts/themes/sand-signika.js"></script>
<?}?>

    <link href="/css/fotorama.css" rel="stylesheet">
    <script src="/js/fotorama.js"></script>


    <script src="/js/jStarbox/jstarbox.js"></script>
    <link rel="stylesheet" href="/css/starbox.css">



    <script src="/js/get_pdf_statistics.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
               /**
         * Create a global getSVG method that takes an array of charts as an argument
         */
        $(function() {
            Highcharts.setOptions({
            	
                xAxis: {
                    labels: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: "Количество ответов"
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
                        showInLegend: true
                    },
                    column:{
                        cursor:'pointer',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend:{
                    enabled:true,
                    verticalAlign: 'middle',
                    align:'right',
                    layout:'vertical',
                    width:200,
                    itemMarginTop: 5,
                    itemMarginBottom: 5,
                    labelFormatter: function() {
                        var words = this.name.split(/[\s]+/);
                        var numWordsPerLine = 2;
                        var str = [];

                        for (var word in words) {
                            if (word > 0 && word % numWordsPerLine == 0)
                                str.push('<br>');

                            str.push(words[word]);
                        }

                        return str.join(' ');
                    }
                },

                credits:{
                    enabled:false
                }
                

            });
        })



    </script>

<?
$qsr = $this->getCountQuizStat($model->quiz_id, $model->hash);
$qsr['open'] =$this->getYaMetrics($model->hash);
$percentage = number_format($qsr['close']/$qsr['open']*100, 1);
?>
    <h2><?php echo Yii::t('app', ucfirst($model->type)); ?> &#171;<?php echo $model->title; ?>&#187;</h2>
    <div id="app-stat-wrapper">
        <div id="app-stat-percentage">
            <div id="app-stat-percentage-first-line">
                <span id="app-stat-percentage-first-line-title">Процент участия</span>
                <span id="app-stat-percentage-first-line-percent"><?=$percentage?>%</span>
            </div>
            <div id="app-stat-percentage-progress-bar">
                <div class="meter">
                    <span style="width: <?=$percentage?>%"></span>
                </div>
            </div>
        </div>
        <div id="app-stat-data-counters">
            <div class="app-stat-counter-block">
                <span class="app-stat-counter-number"><?=$qsr['open']?></span>
                <span class="app-stat-counter-title">Открыли страницу опроса</span>
            </div>
            <div class="app-stat-counter-block">
                <span class="app-stat-counter-number"><?=$qsr['start']?></span>
                <span class="app-stat-counter-title">Начали проходить опрос</span>
            </div>
            <div class="app-stat-counter-block">
                <span class="app-stat-counter-number"><?=$qsr['close']?></span>
                <span class="app-stat-counter-title">Завершили опрос</span>
            </div>
            <div class="app-stat-counter-block">
                <span class="app-stat-counter-number"><?=$qsr['anonym']?></span>
                <span class="app-stat-counter-title">Анонимных ответов</span>
            </div>
        </div>

    </div>
    <h3><?php echo Yii::t('app', 'Statistics '.$model->type); ?></h3>
<?php if($questions): ?>
    <div class="step-pane group-border-dashed form-horizontal">
    <form action="/quiz/pdf" id="svg-submit-form" method="post">
    </form>
    <div style="float: right;
                margin-right: 30px;
                position: relative;
                background: white;
                top: -45px;
                padding: 5px;">Выберите тему: <select name="theme" onchange="location = this.options[this.selectedIndex].value;">
            <option value="?theme=grid_light" <?echo (!isset($_GET['theme'])||$_GET['theme']=='grid_light')?"selected":""; ?>>Светлая</option>
            <option value="?theme=sand_signika" <?echo (isset($_GET['theme']) && $_GET['theme']=='sand_signika')?"selected":""; ?>>Песочная</option>
        </select>
    </div>
    <link rel="stylesheet" type="text/css" href="/js/select.bootstrap/css/bootstrap-select.css">
    <script type="text/javascript" src="/js/select.bootstrap/js/bootstrap-select.js"></script>
    <script>
        $(document).on("load",function(){
            $('.selectpicker').selectpicker();

        })
        $(document).on("change", ".selectpicker", function(){
            var omi_aud_id = $(this).val();
            if(omi_aud_id == 0) location = "/quiz/<?=$model->quiz_id?>/statistics";
            else location = "/quiz/<?=$model->quiz_id?>/statistics?omi_aud_id="+omi_aud_id;
        })
    </script>
    <?
    if($omiAudModel){
    ?>
        <div style="float: right;
                margin-right: 30px;
                position: relative;
                background: white;
                top: -45px;
                padding: 5px;">
            Выберите аудиторию: <select class="selectpicker" data-width="300px" data-show-subtext="true" title='Аудитории OMI'>
                <option value="0">Общее</option>
                <?
                $o = 1;
                foreach($omiAudModel as $item)
                {
                    $subtext = $item->age_from.'-'.$item->age_to.' Пол:'.$item->getField('sex').' '.$item->getField('education').' '.$item->getField('jobsphere').' '.$item->getField('evaluation').
                        ' '.$item->getField('citysize').' '.$item->getField('region').' '.$item->getField('city');
                ?>
                    <option value="<?=$item->id?>" data-subtext="<?=$subtext?>" <?echo $_GET['omi_aud_id']==$item->id?'selected':'';?>>Целевая аудитория OMI <?=$o?></option>
                <?
                    ++$o;
                }
                ?>
            </select>
        </div>
    <?}?>
    <?$i=0;?>
    <?php foreach($questions as $q=>$question): ?>
        <div class="form-group actual">
        <div class="col-sm-12 specPadding">
        <p class="question_text"><?php echo $q+1; ?>. <?php $q_text = $question['question']->text; echo $question['question']->text; ?></p>
        <?php if(isset($question['question']->pictures) && $question['question']->pictures): ?>
            <div>
                <?php foreach ($question['question']->pictures as $p => $image): ?>
                    <?php echo CHtml::image(QuestionMedia::getPath().'/'.$image['link'], $image['link'], array('class'=>'img-polaroid')) ?>
                <?php endforeach; ?>
            </div><br/>
        <?php endif; ?>
        <ul class="unstyled<?php if($question['question']->type != Question::TYPE_SCALE_SCORE): ?> thumbnails<?php endif; ?>" style="list-style: none">

        <?php foreach($question['answers'] as $aud=>$audience): ?>
            <?php if(array_filter(array_values($audience))): ?>
                <li<?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?> class="stat-audience-scale-score"<?php endif; ?>>
                <div<?php if($question['question']->type != Question::TYPE_SCALE_SCORE): ?> class="thumbnail"<?php endif; ?>>

                <?php if($aud == end(array_keys($question['answers']))): ?>
                    <total><?php echo Yii::t('app', 'Total statistics'); ?></total>
                <?php else: ?>
                    <aud><?php echo Yii::t('app', 'Target audience'); ?> №<?php echo $aud+1; ?></aud>
                <?php endif; ?>

                <?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?>
                    <?php $this->widget('bootstrap.widgets.TbBadge', array(
                        'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
                        'label'=>$audience['other'].'%',
                        'htmlOptions'=>array(
                            'data-perc'=>$audience['other']
                        )
                    )); ?>
                <?php endif; ?>

                <?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?>
                    <!--                                            --><?php //$this->widget('bootstrap.widgets.TbProgress', array(
//                                                'type'=>'info', // 'info', 'success' or 'danger'
//                                                'percent'=>$audience['other'], // the progress
//                                                'htmlOptions'=>array('class'=>'col-sm-9', 'title'=>$audience['other'].'%', 'rel'=>"tooltip"),
//                                            )); ?>

                    <script>
                        $(function(){
                            $(".starbox<?=$i?>").starbox({
                                average: <?=$audience['other']/100;?>,
                                changeable: false,
                                autoUpdateAverage: true,
                                ghosting: true
                            });
                        })
                    </script>

                    <div class="starbox<?=$i?>" style="display: inline-block;
margin-left: 30px;
position: relative;
top: 10px;"></div>
                <?php elseif($question['question']->type == Question::TYPE_ANSWPHOTO): ?>
                    <div class="fotorama" data-width="700" data-ratio="700/467" data-max-width="100%" data-nav="thumbs">
                        <?php foreach($audience as $photo): ?>
                            <?php echo CHtml::image(Application::getPath().basename($photo), $photo); ?>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <div style="float:right;">
                        <?php
                        Echo CHtml::dropDownList(
                            'chart_type'.$i,
                            "",
                            array('pie' => 'Пирог разноцветный',
                                'pie_monochrome' => 'Пирог монохромный',
                                'donut' => 'Бублик',
                                'column' => 'Колонки',
                                'bar' => 'Бар',
                                'column_3d' => '3D Колонки',
                                'pie_3d' => '3D Пирог',
                                'donut_3d' => '3D Бублик'
                            ),
                            array('class' => 'chart_type', 'group' => "{$audience['group']}",)
                        );

                        # Здесь формируем массивы с данными
                        $cData['column'] = "[";
                        $cData['pie']	=	"[{type: 'pie',name: 'Количество ответов', data:[";
                        foreach ($question['question']->answers as $a => $answer) {
                            $cData['column'] .= "{name:'".$answer->text."', data:[".$audience[$answer->id]."], dataLabels:{enabled:true}},";
                            $cData['pie'] .= "['".$answer->text."', ".$audience[$answer->id]."],";

                        }
                        $cData['donut'] = $cData['pie']."], innerSize: '50%'}]";
                        $cData['pie'] .= "]}]";
                        $cData['pie_monochrome'] = $cData['pie'];
                        $cData['column'] .= "]";
                        $cData['bar'] = $cData['column'];
                        $cData['graph']	=	"['',";
                        foreach ($question['question']->answers as $a => $answer) {
                            $cData['graph']	.=	"'{$answer->text}',";
                        }
                        $cData['graph']	.=	"],['',";
                        foreach ($question['question']->answers as $a => $answer) {
                            $cData['graph']	.=	"{$audience[$answer->id]},";
                        }
                        $cData['graph']	.=	"],";
                        ?>
                    </div><div style="clear: both;"></div>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            chart<?=$i;?> = new Highcharts.Chart({

                                chart: {
                                    renderTo: 'container<?=$i;?>',
                                    type:'pie'
                                },
                                title:
                                {
                                    text:null,
                                    useHTML:true
                                },

                                series:<?=$cData['pie']?>,
                                exporting: {
                                    allowHTML: true,
                                    scale:1.5,
                                    buttons:{
                                        contextButton:{
                                            menuItems: [{
                                                text: 'Сохранить как PNG',
                                                onclick: function () {
                                                    this.exportChart({}, {
                                                        title: {
                                                            text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                        }
                                                    });
                                                 }
                                             },
                                             {
                                                text: 'Сохранить как PDF',
                                                onclick: function () {
                                                    this.exportChart({type:'application/pdf'}, {
                                                        title: {
                                                            text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                        }
                                                        
                                                    });
                                                 }
                                             }]
                                            
                                        }
                                    }
                                }


                            });
                        })

                        var type<?=$i;?> = {
                            pie:<?=$cData['pie']?>,
                            pie_monochrome:<?=$cData['pie']?>,
                            column:<?=$cData['column']?>,
                            bar:<?=$cData['bar']?>,
                            donut:<?=$cData['donut']?>,
                            column_3d:<?=$cData['column']?>,
                            pie_3d:<?=$cData['pie']?>,
                            donut_3d:<?=$cData['donut']?>
                        }
                        $(document).on("change", "#chart_type<?=$i?>", function(){
                            var chart_type = $(this).val();
                            switch(chart_type){
                                case 'donut':
                                    var options = {
                                        plotOptions: {
                                            pie: {
                                                startAngle: -90,
                                                endAngle: 90,
                                                center: ['50%', '75%']
                                            }
                                        },
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type:'pie'
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },

                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }

                                    };
                                    break;
                                case 'pie':
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type:'pie'
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },

                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    };
                                    break;
                                case 'pie_monochrome':
                                function get_monochrome_colors() {
                                    var colors = [],
                                        base = Highcharts.getOptions().colors[0],
                                        i;

                                    for (i = 0; i < 10; i += 1) {
                                        // Start out with a darkened base color (negative brighten), and end
                                        // up with a much brighter color
                                        colors.push(Highcharts.Color(base).brighten((i - 3) / 7).get());
                                    }
                                    return colors;
                                };
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type:'pie'
                                        },
                                        plotOptions: {
                                            pie: {
                                                colors:get_monochrome_colors()
                                            }
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },
                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 'column':
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type:'column'
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },

                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    };
                                    break;
                                case 'bar':
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type:'bar'
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },

                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    };
                                    break;
                                case 'column_3d':
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type: 'column',
                                            margin: 75,
                                            options3d: {
                                                enabled: true,
                                                alpha: 15,
                                                beta: 15,
                                                depth: 50,
                                                viewDistance: 25
                                            }
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },
                                        plotOptions: {
                                            column: {
                                                depth: 25
                                            }
                                        },
                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 'pie_3d':
                                    var options = {
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type: 'pie',
                                            options3d: {
                                                enabled: true,
                                                alpha: 45,
                                                beta: 0
                                            }
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },
                                        plotOptions: {
                                            pie: {
                                                depth: 35
                                            }
                                        },
                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 'donut_3d':
                                    var options = {
                                        plotOptions: {
                                            pie: {
                                                innerSize: 100,
                                                depth: 45
                                            }
                                        },
                                        title:
                                        {
                                            text:null,
                                            useHTML:true
                                        },
                                        chart: {
                                            renderTo: 'container<?=$i;?>',
                                            type: 'pie',
                                            options3d: {
                                                enabled: true,
                                                alpha: 45
                                            }
                                        },

                                        series: type<?=$i;?>[chart_type],
                                        exporting: {
                                            allowHTML: true,
                                            scale:1.5,
                                            buttons:{
                                                contextButton:{
                                                    menuItems: [{
                                                        text: 'Сохранить как PNG',
                                                        onclick: function () {
                                                            this.exportChart({}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                            });
                                                         }
                                                     },
                                                     {
                                                        text: 'Сохранить как PDF',
                                                        onclick: function () {
                                                            this.exportChart({type:'application/pdf'}, {
                                                                title: {
                                                                    text: "<?php echo $q+1; ?>. <?php echo addslashes($q_text);?>"
                                                                }
                                                                
                                                            });
                                                         }
                                                     }]
                                                    
                                                }
                                            }
                                        }
                                    };
                                    break;



                            }
                            chart<?=$i;?> = new Highcharts.Chart(options);
                        })
                    </script>

                    <div id="container<?=$i;?>" style="width: 800px; height: 400px;"></div>

                    <?php
                    /*
                        <?php echo $this->renderPartial('_tab_chart', array(
                            'chart'=>CHtml::image('/'.  $audience['chart'], Yii::t('app', 'Statistics question').' '.Yii::t('app', 'Target audience').' №'.$aud),
                            'pie'=>CHtml::image('/'.  $audience['pie'], Yii::t('app', 'Statistics question').' '.Yii::t('app', 'Target audience').' №'.$aud))
                        ); ?>
                      */
                    ?>
                    <?++$i;?>
                <?php endif; ?>
                </div>
                </li>

            <?php endif; ?>
        <?php endforeach; ?>
        </ul>
        </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><?php echo Yii::t('app', 'Not answers'); ?></p>
<?php endif; ?>