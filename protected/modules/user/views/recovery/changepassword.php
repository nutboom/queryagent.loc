<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Change Password");
$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Change Password"),
);
?>

<h1><?php echo UserModule::t("Change password"); ?></h1>

<div class="form">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'recovery-form',
	'type'=>'horizontal',
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>
	<?php echo $form->errorSummary(array($model)); ?>

	<?php echo $form->passwordFieldRow($model,'password',array('class'=>'span5','size'=>20,'maxlength'=>20)); ?>
	<?php echo $form->passwordFieldRow($model,'verifyPassword',array('class'=>'span5','size'=>20,'maxlength'=>20)); ?>


	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=> UserModule::t('Save'),
		)); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->