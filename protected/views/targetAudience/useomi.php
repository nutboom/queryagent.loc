<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20.03.15
 * Time: 9:32
 */
?>
<?php
//$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
//    'id'=>'target-audience-form',
//    'type'=>'horizontal',
//    'htmlOptions'=>array('class'=>'group-border-dashed'),
//    'enableAjaxValidation'=>false,
//));
?>
    <link rel="stylesheet" type="text/css" href="/js/select.bootstrap/css/bootstrap-select.css">
    <script type="text/javascript" src="/js/select.bootstrap/js/bootstrap-select.js"></script>
<script>
function number_format( number, decimals, dec_point, thousands_sep ) {  // Format a number with grouped thousands
    // 
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ",";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


    return km + kw + kd;
}

    $(document).on('ready',function(){
        $('.selectpicker').selectpicker();

        $(document).on("change","select, input",function(){
            var dataS = $("#omi_form").serializeArray();
            $.ajax({
                url: "omigetcountrespondents",
                type: "POST",
                data: dataS
            }).done(function(data) {
                var number = number_format(data, 0, '.', ' ');
                $("#count_respondents").html(number);
                
                $("input[name='respondents_count']").val(data);
            });
        })

        $(document).on("change","select[name='regions[]']",function(){
            var dataS = $("#omi_form").serializeArray();
            $.ajax({
                url: "omigetcities",
                type: "POST",
                data: dataS
            }).done(function(data) {
                if (data == 0){
                    $("#last_block").hide(200);
                    $("#last_block").html('');
                }
                else{
                    $("#last_block").html(data);
                    $('#last_block > select').selectpicker();
                    $("#last_block").show(200);
                    $("#last_block").removeAttr("style");

                }

            });
        })
    })


</script>
<style>
    .aud_attr{
        margin: 10px;
        padding: 10px;
        border: 1px dashed rgb(224, 224, 224);
        display: inline-block;
        width: 30%;
    }

    .aud_attr_limit{
        margin: 10px;
        padding: 10px;
        border: 1px dashed rgb(224, 224, 224);
    }
</style>
    <div class="step-pane">
        <div class="no-padding nCForm-group">
            <div class="col-sm-10">
                <h3 class="hthin">
                    <span id="respondents_info"><?php echo Yii::t('app','Total respondents'); ?>:&nbsp;<b id="count_respondents"><?php echo $this->countOmiRespondents(); ?></b> человек</span>
                </h3>
            </div>
            <div class="clearfix"></div>
        </div>

        <form action="omisaveaud" id="omi_form" method="post">
            <input type="hidden" name="quiz_id" value="<?=$quiz->quiz_id?>">
            <input type="hidden" name="respondents_count" value="<?php echo $this->countOmiRespondents(); ?>">

            <div class="aud_attr">
                Размер города:<br>
                <select name="citysize[]" multiple class="selectpicker" data-header="Выберите размер города" data-selected-text-format="count>2">
                    <?
                    foreach($citysize as $value)
                    {
                        ?>
                        <option value="<?=$value['id']?>"><?=$value['title']?></option>
                    <?
                    }
                    ?>
                </select>
            </div>

            <div class="aud_attr" id="pre_last_block">
                Регион:<br>
                <select name="regions[]" multiple class="selectpicker" data-header="Выберите регион" data-selected-text-format="count>2" data-live-search="true" data-size="10">
                    <?
                    foreach($regions as $value)
                    {
                        ?>
                        <option value="<?=$value['id']?>"><?=$value['title']?></option>
                    <?
                    }
                    ?>
                </select>
            </div>

            <div class="aud_attr" style="display: none; overflow: inherit;" id="last_block">

            </div><br>

            <div class="aud_attr">
                Возраст:<br>
                от <input type="text" value="0" name="age_from" style="width: 80px; margin-right: 10px; margin-left: 5px;">   до <input type="text" value="0" name="age_to" style="width: 80px; margin-left: 5px;">
            </div>

            <div class="aud_attr">
                Пол:<br>
                <select name="sex" class="selectpicker" data-header="Выберите пол">
                    <option value="0">Любой</option>
                    <option value="1">Мужской</option>
                    <option value="2">Женский</option>
                </select>
            </div><br>

            <div class="aud_attr">
                Образование:<br>
                <select name="education[]" multiple class="selectpicker" data-header="Выберите образование" data-selected-text-format="count>2">
                    <?
                    foreach($education as $value)
                    {
                        ?>
                        <option value="<?=$value['id']?>"><?=$value['title']?></option>
                    <?
                    }
                    ?>
                </select>
            </div>

            <div class="aud_attr">
                Сфера деятельности:<br>
                <select name="jobsphere[]" multiple class="selectpicker" data-size="10" data-header="Выберите сферу деятельности" data-selected-text-format="count>2">
                    <?
                    foreach($job_sphere as $value)
                    {
                        ?>
                        <option value="<?=$value['id']?>"><?=$value['title']?></option>
                    <?
                    }
                    ?>
                </select>
            </div>

            <div class="aud_attr">
                Материальное положение семьи:<br>
                <select name="evaluation[]" multiple class="selectpicker" data-header="Выберите мат. положение" data-selected-text-format="count>2">
                    <?
                    foreach($evaluation as $value)
                    {
                        ?>
                        <option value="<?=$value['id']?>"><?=$value['title']?></option>
                    <?
                    }
                    ?>
                </select>
            </div>



            <div class="aud_attr_limit">
                Желаемый размер выборки (не менее 100 чел.)<input type="text" value="0" name="limit" style="width: 70px; margin-left: 5px;">
            </div>

            <input type="submit" value="Сохранить" class="btn btn-primary" style="margin: 10px;">


        </form>




    </div>
<?php //$this->endWidget(); ?>