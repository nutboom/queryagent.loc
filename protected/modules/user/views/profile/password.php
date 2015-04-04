<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");

$breadcrumbs=array(
	UserModule::t("Profile"),
);
$this->breadcrumbs=$breadcrumbs;

?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'profile-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Change password'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>


		<?php echo $form->passwordFieldRow($model,'oldPassword',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>

		<?php echo $form->passwordFieldRow($model,'password',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>

		<?php echo $form->passwordFieldRow($model,'verifyPassword',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>



		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=> Yii::t('app', 'Change'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

