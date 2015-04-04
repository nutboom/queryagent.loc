<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'tariffs-form',
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


		<?php echo $form->textFieldRow($model,'name',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'cost',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'free_month',array('class'=>'form-control','maxlength'=>150)); ?>


		<?php echo $form->textFieldRow($model,'minimum',array('class'=>'form-control','maxlength'=>150)); ?>



		<?php echo $form->textFieldRow($model,'limit_users',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'limit_respondents',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'limit_groups',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'limit_quizs',array('class'=>'form-control','maxlength'=>150)); ?>

		<?php echo $form->textFieldRow($model,'limit_companys',array('class'=>'form-control','maxlength'=>150)); ?>


		<?php echo $form->dropDownListRow($model,'limit_templates', array("yes" => Yii::t('app','Yes'), "no" => Yii::t('app','No'))); ?>
		<?php echo $form->dropDownListRow($model,'limit_brand_quiz', array("yes" => Yii::t('app','Yes'), "no" => Yii::t('app','No'))); ?>
		<?php echo $form->dropDownListRow($model,'limit_brand_site', array("yes" => Yii::t('app','Yes'), "no" => Yii::t('app','No'))); ?>

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
