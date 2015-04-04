<?php Yii::app()->clientScript->registerScript('initTooltip', "
    $(document).ready(function() {
        $('.badge').tooltip();
    });
"); ?>

<?php
/* @var $this GroupRespondentsController */
/* @var $model GroupRespondents */

$this->pageTitle=Yii::app()->name . ' / '. Yii::app()->getModule('respondent')->t('Groups respondents') . ' / '.Yii::t('app', 'Create');
$this->breadcrumbs=array(
	Yii::app()->getModule('respondent')->t('Groups respondents')=>array('index'),
	RespondentModule::t('Participants'),
);
$this->menu=array(
	array('label'=>Yii::app()->getModule('respondent')->t('List groups'), 'url'=>array('index')),
	array('label'=>Yii::app()->getModule('respondent')->t('Create group'),'url'=>array('create')),
	array('label'=>Yii::app()->getModule('respondent')->t('Update group'),'url'=>Yii::app()->createUrl("respondent/groups/update/id/".$model->id)),
);
?>

<h1><?php echo RespondentModule::t('Group respondents'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'type'=>'bordered',
	'data'=>$model,
	'attributes'=>array(
                array('name'=>'title', 'label'=>Yii::t('app', 'Title')),
                array('value'=>$model->client->name, 'label'=>Yii::t('app', 'Client Name'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
                array('value'=>$model->manager->profile->last_name.' '.$model->manager->profile->first_name, 'label'=>Yii::t('app', 'Manager'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
                array('value'=>Yii::app()->dateFormatter->format('dd.MM.yyyy HH:mm:ss', $model->created_at), 'label'=>Yii::t('app', 'Date Created'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
                array('value'=>Yii::app()->dateFormatter->format('dd.MM.yyyy HH:mm:ss', $model->updated_at), 'label'=>Yii::t('app', 'Date Updated'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
	),
)); ?>

<h3><?php echo RespondentModule::t('Participants'); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$respondents,
    'template'=>"{items}",
    'columns'=>array(
        array('name'=>'fullName', 'header'=>Yii::t('app','Name')),
        array('name'=>'phone_number', 'header'=>Yii::t('app','Phone number')),
        array('name'=>'email_actual', 'header'=>Yii::t('app','E-mail')),
        array('name'=>'indicatorConfirmPhone', 'type'=>'html', 'header'=>'', 'htmlOptions'=>array('style'=>'width: 30px;')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{delete}',
            'deleteButtonIcon'=>'icon-remove-sign',
            'deleteButtonUrl'=>'Yii::app()->createUrl("respondent/groups/remove/id/".$data->primaryKey)',
        ),
    ),
)); ?>