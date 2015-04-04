<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Finanse");

$breadcrumbs=array(
	UserModule::t("Finanse")=>array('/user/finance'),
	UserModule::t("Sub Users")=>array('/user/finance/sub'),
	UserModule::t("Create Sub User"),
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
		'id'=>'form-addsubuser',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo UserModule::t("Create Sub User"); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary(array($model,$profile)); ?>


		<?php
			$profileFields=$profile->getFields();
			if ($profileFields) {
				foreach($profileFields as $field) {
					?>
						<?php
							if ($widgetEdit = $field->widgetEdit($profile)) {
								echo $widgetEdit;
							} elseif ($field->range) {
								echo $form->dropDownListRow($profile,$field->varname,Profile::range($field->range));
							} elseif ($field->field_type=="TEXT") {
								echo $form->textAreaRow($profile,$field->varname,array('class'=>'form-control','rows'=>6, 'cols'=>50));
							} else {
								echo $form->textFieldRow($profile,$field->varname,array('class'=>'form-control','size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
							}
						?>
						<?php echo $form->error($profile,$field->varname); ?>
				<?php
				}
			}
		?>

		<?php echo $form->textFieldRow($model,'username',array('class'=>'form-control','size'=>20,'maxlength'=>20)); ?>

		<?php echo $form->textFieldRow($model,'email',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>

		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=> UserModule::t('Create'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>
