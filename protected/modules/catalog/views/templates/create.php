<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', 'Quizs templates');
$this->breadcrumbs=array(
	Yii::t('app','Dict') => array('/catalog'),
    Yii::t('app','Quizs templates'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('update')),
);
?>

<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'quiz-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Create new quiz template'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>


		<?php echo $form->textFieldRow($model,'title',array('class'=>'form-control','maxlength'=>150)); ?>


		<?php echo $form->dropDownListRow($model, 'type', Templates::itemAlias('QuizType')); ?>
		

		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>$model->isNewRecord ? Yii::t('app','Next') : Yii::t('app','Save'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>