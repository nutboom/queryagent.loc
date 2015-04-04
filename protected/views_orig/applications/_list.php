<?php Yii::app()->clientScript->registerScript('re-install-date-picker', "
        function reinstallDatePicker(id, data) {
            $('#datepicker_for_date_created').datepicker(jQuery.extend(jQuery.datepicker.regional['ru']));;
            $('#datepicker_for_date_filled').datepicker(jQuery.extend(jQuery.datepicker.regional['ru']));;
            $('#datepicker_for_date_closed').datepicker(jQuery.extend(jQuery.datepicker.regional['ru']));;
        }
    ");
?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'id'=>'application-grid',
    'dataProvider'=>$model->search($quiz ?  $quiz->quiz_id : NULL),
    'filter'=>$model,
    'afterAjaxUpdate' => 'reinstallDatePicker',
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array(
            'name'=>'respondent_id',
            'value'=>'Yii::app()->getModule("respondent")->respondent($data->respondent_id)->fullName ? Yii::app()->getModule("respondent")->respondent($data->respondent_id)->fullName : Yii::t("app", "Anonym respondent")',
            'visible'=>!intval($model->respondent_id)
        ),
        array(
            'name'=>'quiz_id',
            'value'=>'$data->quiz->title',
            'visible'=>$quiz == NULL
        ),
        array(
            'name'=>'date_created',
            'value'=>'Utils::unpack_datetime($data->date_created)',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'attribute'=>'date_created',
                'language' => 'ru',
                'htmlOptions' => array(
                    'id' => 'datepicker_for_date_created',
                    'size' => '10',
                ),
                'defaultOptions' => array(  // (#3)
                    'showOn' => 'focus',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                )
            ),
            true),
        ),
        array(
            'name'=>'date_filled',
            'value'=>'(isset($data) && $data->date_filled) ? Utils::unpack_datetime($data->date_filled) : Yii::t("app", "No Date Filled")',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'attribute'=>'date_filled',
                'language' => 'ru',
                'htmlOptions' => array(
                    'id' => 'datepicker_for_date_filled',
                    'size' => '10',
                ),
                'defaultOptions' => array(  // (#3)
                    'showOn' => 'focus',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                )
            ),
            true),
        ),
        array(
            'name'=>'date_closed',
            'value'=>'(isset($data) && $data->date_closed) ? Utils::unpack_datetime($data->date_closed) : Yii::t("app", "No Date Closed")',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'attribute'=>'date_closed',
                'language' => 'ru',
                'htmlOptions' => array(
                    'id' => 'datepicker_for_date_closed',
                    'size' => '10',
                ),
                'defaultOptions' => array(  // (#3)
                    'showOn' => 'focus',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                )
            ),
            true),
        ),
        array(
            'name'=>'state',
            'value'=>'Application::itemAlias("StatusApplication", "$data->state")',
            'filter'=>Application::itemAlias('StatusApplication'),
        ),
        array(
            'name'=>'is_appeal',
            'value'=>'Application::itemAlias("Appeal", "$data->is_appeal")',
            'filter'=>Application::itemAlias('Appeal'),
        ),
        array(
            'name'=>'is_true_answer',
            'value'=>'Application::itemAlias("CorrectAnswerCheckQuestionApplication", "$data->is_true_answer")',
            'filter'=>Application::itemAlias('CorrectAnswerCheckQuestionApplication'),
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{view}',
            'viewButtonUrl'=>'Yii::app()->createUrl("/".$data->quiz->type."/".$data->quiz_id."/Applications/".$data->primaryKey)',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>