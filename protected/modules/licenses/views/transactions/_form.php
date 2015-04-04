<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'transaction-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo $action; ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>


		<?php echo $form->textFieldRow($model,'user',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'summ',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->dropDownListRow($model,'status', Transactions::itemAlias("", true)); ?>


		<?php echo $form->textFieldRow($model,'date_open',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'date_close',array('class'=>'form-control','maxlength'=>150)); ?>


		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>$model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>
