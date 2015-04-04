<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app', 'Update '.$model->type);

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$model->type.'/'.$model->quiz_id.'/update'),
	Yii::t('app', 'Wizard Audience') => array('/'.$model->type.'/'.$model->quiz_id.'/targetAudience'),
	Yii::t('app', 'Wizard Questions') => array('/'.$model->type.'/'.$model->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Collection') => array('/'.$model->type.'/'.$model->quiz_id.'/collection'),
	Yii::t('app', 'Wizard Launch') => array(array('/'.$model->type.'/'.$model->quiz_id.'/launch'), "active"),
);

if ($model->state != Quiz::STATE_EDIT) {
	$this->menu=array(
		array('label'=>Yii::t('app', 'Results'),'url'=>array($model->type."/".$model->quiz_id."/Applications")),
		array('label'=>Yii::t('app', 'Statistics'),'url'=>array($model->type."/".$model->quiz_id."/statistics")),
		array('label'=>Yii::t('app', 'Comments'),'url'=>array($model->type."/".$model->quiz_id."/comments")),
		array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($model->type."/".$model->quiz_id."/export")),
	);
}
?>


<?php
	$attributes = $model->attributeLabels();
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
				<h3 class="hthin"><?php echo Yii::t('app', 'Wizard Launch'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>


		<?php echo $form->errorSummary($model); ?>

		<?php
			if ($model->state == Quiz::STATE_EDIT) {
				if(count($model->audience) > 0 && $model->countQuestions() > 0) {
					// может запустить
		  			?>
						<input type="hidden" name="Quiz[state]" value="moderation">
						<div class="form-group">
							<div class="col-sm-2">
								<?php $this->widget('bootstrap.widgets.TbButton', array(
									'buttonType'=>'submit',
									'type'=>'primary',
									'label'=>Yii::t('app','Launch this'),
								)); ?>
							</div>
						</div>
		  			<?php
		  		}
		  		else {
					// не получает запустить
					?>
		            	вы не можете запустить опрос потому что нет вопросов и аудитории
					<?php
				}
			}
			else if ($model->state == Quiz::STATE_WORK) {
				// на заполнении
				?>
		        	опрос находится на заполнении в данный момент
				<?php
			}
			else if ($model->state == Quiz::STATE_MODERATION) {
				// на заполнении
				?>
		        	опрос находится на модерации у оператора
				<?php
			}
			else {
				// заполнен и закрыт
				?>
					опрос успешно заполнен и закрыт

				<?php
			}
		?>
	</div>
<?php $this->endWidget(); ?>