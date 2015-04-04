<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'client-form',
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

		<div class="form-group">
			<?php echo $form->labelEx($model,'name',array('class'=>'col-sm-2 control-label')); ?>
			<div class="col-sm-6">
				<?php echo $form->textField($model,'name',array('class'=>'form-control')); ?>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group">
			<div class="col-sm-2">
				<?php
					if (isset($_GET['quiz'])) {
						$label = Yii::t('app','Create and return to quiz');
					}
					else if (isset($_GET['mission'])) {
						$label = Yii::t('app','Create and return to mission');
					}
					else if ($respondets) {
						$label = Yii::t('app','Create and return to group of respondents');
					}
					else {
						$label = $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save');
					}
					$this->widget('bootstrap.widgets.TbButton', array(
						'buttonType'=>'submit',
						'type'=>'primary',
						'label'=>$label,
					));
				?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>
