<?php
$this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents') . ($model->blocked ? (' / '.RespondentModule::t('Blocked respondent')) : '');
if($model->blocked)
    $this->breadcrumbs=array(
            RespondentModule::t('Respondents')=>array('index'),
            RespondentModule::t('Blocked respondent')
    );
else
    $this->breadcrumbs=array(
        RespondentModule::t('Respondents'),
    );

$this->layout='//layouts/column2';

$this->menu=array(
	array('label'=>Yii::app()->getModule('respondent')->t('Groups respondents'),'url'=>array('/respondent/groups'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
	array('label'=>RespondentModule::t("Statuses Respondents"),'url'=>array('/respondent/statuses')),
	array('label'=>RespondentModule::t("Blocked respondent"),'url'=>array('blocked'), 'visible'=>!$model->blocked),
);
?>

<h1><?php echo $model->blocked ? RespondentModule::t("List Blocked Respondents") : RespondentModule::t("List Respondents"); ?></h1>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'respondent-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$model->search(),
        //'filter'=>$model,
        'template'=>"{items}\n{pager}",
        'pagerCssClass'=>'pagination pagination-right',
        'columns'=>array(
		array(
			'name' => 'first_name',
			'value' => 'UHtml::markSearch($data,"last_name")." ".UHtml::markSearch($data,"first_name")',
		),
                array(
                    'name'=>'phone_number',
                    'htmlOptions'=>array('style'=>'width: 150px'),
                ),
                array(
			'name'=>'phone_is_confirmed',
			'value'=>'Respondent::itemAlias("PhoneConfirmedStatus",$data->phone_is_confirmed)',
			//'filter'=>User::itemAlias("AdminStatus"),
                        'htmlOptions'=>array('style'=>'width: 10px'),
		),
		'email_actual',
                array(
                    'header'=>Yii::t('app','Number quizs'),
                    'type' => 'raw',
			        'value'=>'$data->showCountQuiz()',
                    'htmlOptions'=>array('style'=>'width: 90px'),
		),
		array(
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'template'=>'{view} {payments} {applications} {sms} {access}',
                    'buttons'=>array
                    (
                        'view' => array(
                            'visible'=>'$data->sex != Respondent::NONE'
                        ),
                        'payments' => array
                        (
                            'label'=>RespondentModule::t('Respondent Payments'),
                            'url'=>'Yii::app()->createUrl("/respondent/payments", array("id"=>$data->id))',
                            'icon'=>'icon-random',
                            'visible'=>'$data->sex != Respondent::NONE'
                        ),
                        'applications' => array
                        (
                            'label'=>RespondentModule::t('Respondent aplications'),
                            'url'=>'array("applications","id"=>$data->id)',
                            'icon'=>'icon-file',
                            'visible'=>'$data->sex != Respondent::NONE'
                        ),
                        'sms' => array
                        (
                            'label'=>RespondentModule::t('Retry send sms code to respondent'),
                            'url'=>'array("sendSms","id"=>$data->id)',
                            'icon'=>'icon-envelope',
							'options' => array( 'ajax' => array('type' => 'post', 'url'=>'js:$(this).attr("href")', 'success' => 'js:function(data) { $.fn.yiiGridView.update("respondent-grid")}')),
                            'visible' => '!$data->phone_is_confirmed '
                        ),
                        'access' => array
                        (
                            'label'=>RespondentModule::t('Deny access to respondent on service'),
                            'url'=>'array("denyAccess","id"=>$data->id)',
                            'icon'=>'icon-remove-circle',
                            'options' => array( 'confirm' => RespondentModule::t('Are you sure you want to block this respondent?'), 'ajax' => array('type' => 'post', 'url'=>'js:$(this).attr("href")', 'success' => 'js:function(data) { $.fn.yiiGridView.update("respondent-grid")}')),
                            'visible' => '!$data->blocked'
                        ),
                    ),
                    'htmlOptions'=>array('style'=>'width: 70px'),
                ),
	),
    ));
?>
