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
		'htmlOptions'=>array('class'=>'group-border-dashed','enctype' => 'multipart/form-data'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Change avatar'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php echo $form->errorSummary($model); ?>

		<div class="form-group">
		    <div class="col-sm-1">
				<img src="<?php echo $avatar; ?>" class="nCImage rounded"/>
		    </div>
		    <div class="col-sm-2 avatarElement">
				<button class="btn btn-primary loadAvatar"><?php echo Yii::t('app', 'Attach avatar'); ?></button>
				<?php echo CHtml::fileField('avatar','',array('class'=>'hidden')); ?>
		    </div>
		    <div class="col-sm-3 avatarElement">
				<span class="avatarAfter"><?php echo Yii::t('app', 'File attached'); ?>: <span class="filename">filename</span></span>
		    </div>
		</div>

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

