<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jquery-ui-1.8.20.custom.min.js'); ?>
<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/ui-lightness/jquery-ui-1.8.20.custom.css'); ?>

<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/jqDynaForm.css'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jqDynaForm.js'); ?>

<?php Yii::app()->getClientScript()->registerScriptFile('/js/directory.js'); ?>
<?php Yii::app()->clientScript->registerScript('checkQuestion', "
    $(document).ready(function() {
        ".($jsonModelAnswer ? "$('#answers_on_question').jqDynaForm('set',". $jsonModelAnswer. ");" : '')."
    });
"); ?>

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


    	<fieldset>
			<div class="col-sm-8"><h3><?php echo Yii::t('app','Question'); ?></h3></div>
			<div class="clearfix"></div>

			<?php echo $form->errorSummary(array($model,$modelAnswer)); ?>

			<?php echo $form->textFieldRow($model,'text',array('class'=>'form-control')); ?>

			<div class="col-sm-8"><h3><?php echo Yii::t('app','Answers'); ?></h3></div>
			<div class="clearfix"></div>

			<div id="answers_on_question" class="answers_question">
				<div data-holder-for="answer"></div>
				<div class='clearfix'></div>
							<div class='col-sm-2'></div>
			<div class="col-sm-3"><a href="#" class="addAnswer addLinq"><button class="btn btn-primary"><b class="fa fa-plus"></b>&nbsp;&nbsp;Добавить ответ</button></a></div>
			<div class="clearfix"></div>
			</div>

			<div class="form-group">
				<div class="col-sm-2">
					<?php $this->widget('bootstrap.widgets.TbButton', array(
						'buttonType'=>'submit',
						'type'=>'primary',
						'label'=>$model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'),
					)); ?>
				</div>
			</div>
		</fieldset>
	</div>
<?php $this->endWidget(); ?>



<!-- Subforms library -->
<div style="display:none" id="subforms_library">
    <div data-name="answer" data-label="Answers" class="answer" data-number-answer="number_answer">
    	<?php echo $form->hiddenField($modelAnswer, 'id[]'); ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><span class="number"><?php echo 'number_answer)'; ?></span></label>
			<div class="col-sm-4">
				<?php echo $form->textField($modelAnswer,'text[]',array('class'=>'form-control', 'id'=>'text_number_answer', 'placeholder'=>$modelAnswer->getAttributeLabel("text"))); ?>
			</div>

			<div class="col-sm-2">
				<button class="btn btn-default btn-remove"><b class="fa fa-trash-o"></b></button>
				<button class="btn btn-default btn-add"><b class="fa fa-plus"></b></button>
			</div>

			<div class="col-sm-4">
				<?php echo $form->radioButtonRow($modelAnswer,'is_true[]', array('id'=>'is_true_number_answer')); ?>
			</div>
		</div>
	</div>
</div>
<?php /*
<div style="display:none" id="subforms_library">
    <div data-name="answer" data-label="Answers" class="answer" data-number-answer="number_answer">
        <?php echo $form->hiddenField($modelAnswer,'id[]'); ?>
        <?php echo $form->textFieldRow($modelAnswer,'text[]',array('class'=>'form-control', 'id'=>'text_number_answer', 'placeholder'=>$modelAnswer->getAttributeLabel("text"))); ?>
        <?php echo $form->radioButtonRow($modelAnswer,'is_true[]', array('id'=>'is_true_number_answer')); ?>
    </div>
</div> */ ?>
