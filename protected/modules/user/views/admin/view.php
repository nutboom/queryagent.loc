<?php
$this->pageTitle=Yii::app()->name . ' / '. UserModule::t('Users') . ' / '.$model->username;
$this->breadcrumbs=array(
	UserModule::t('Users')=>array('/user'),
	$model->username,
);


$this->menu=array(
    array('label'=>UserModule::t('Create User'), 'url'=>array('create')),
    array('label'=>UserModule::t('Update User'), 'url'=>array('update','id'=>$model->id)),
    array('label'=>UserModule::t('Delete User'), 'url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>UserModule::t('Are you sure to delete this item?'))),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
);
?>
<h1><?php echo UserModule::t('View User').' "'.$model->username.'"'; ?></h1>

<?php

	$attributes = array(
		'id',
		'username',
	);

	$profileFields=ProfileField::model()->forOwner()->sort()->findAll();
	if ($profileFields) {
		foreach($profileFields as $field) {
			array_push($attributes,array(
					'label' => UserModule::t($field->title),
					'name' => $field->varname,
					'type'=>'raw',
					'value' => (($field->widgetView($model->profile))?$field->widgetView($model->profile):(($field->range)?Profile::range($field->range,$model->profile->getAttribute($field->varname)):$model->profile->getAttribute($field->varname))),
				));
		}
	}

	array_push($attributes,
		'email',
		'phone_number',
		'create_at',
		'lastvisit_at',
		array(
			'name' => 'superuser',
			'value' => User::itemAlias("AdminStatus",$model->superuser),
		),
		array(
			'name' => 'manager',
			'value' => User::itemAlias("ManagerStatus",$model->manager),
		),
		array(
			'name' => 'status',
			'value' => User::itemAlias("UserStatus",$model->status),
		)
	);

	$this->widget('bootstrap.widgets.TbDetailView', array(
		'data'=>$model,
		'attributes'=>$attributes,
	));

?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProviderLicenses,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
    	array('name'=>'id'),
        array('name'=>'tariff', 'header'=>Yii::t('app','Tariff'), 'value'=>'$data->limits->name'),
        array('name'=>'active', 'header'=>Yii::t('app','Is Tariff Active'), value=>'Licenses::aliasActive($data->active)'),
        array('name'=>'date_open', 'header'=>Yii::t('app','Date Open Tariff')),
        array('name'=>'date_expirate', 'header'=>Yii::t('app','Date Expirate Tariff')),
    ),
)); ?>

<h2><?php echo Yii::t('app', 'Add new license'); ?></h2>

<?php
$this->widget('bootstrap.widgets.TbAlert', array(
	'block'=>true, // display a larger alert block?
	'fade'=>true, // use transitions?
	'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
	'alerts'=>array( // configurations per alert type
		'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
	),
)); ?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'type'=>'horizontal',
	'htmlOptions'=>array('class'=>'well'),
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary(array($license)); ?>

	<?php echo $form->dropDownListRow($license,'tariff', CHtml::listData(Tariffs::model()->findAll(), 'id', 'name')); ?>
	<?php echo $form->dropDownListRow($license,'active', array("yes" => Yii::t('app','Yes'), "no" => Yii::t('app','No'))); ?>
	<?php echo $form->textFieldRow($license,'date_open',array('class'=>'span5')); ?>
	<?php echo $form->textFieldRow($license,'date_expirate',array('class'=>'span5')); ?>

	<div class="row buttons">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>UserModule::t('Create'),
	)); ?>
	</div>

<?php $this->endWidget(); ?>
