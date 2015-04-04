<?php Yii::app()->getClientScript()->registerScriptFile('/js/targetAudience.js'); ?>
<script>
    $(document).on('ready',function(){
        var base_type = window.location.hash.replace("#","");
        if (base_type == "own") $('a[data-target="#groupsRespondentsModal"]').trigger('click');
        else if(base_type == "service")$('#use_base').trigger('click');
    })
</script>


<?php
	$attributes = $model->attributeLabels();
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'target-audience-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-10">
				<h3 class="hthin">
					<?php echo Yii::t('app', ucfirst($quiz->type)); ?>&nbsp;&#171;<?php echo $quiz->title; ?>&#187;.
					<span id="respondents_info" style="display: none;"><?php echo Yii::t('app','Total respondents'); ?>:&nbsp;<b id="count_respondents"><?php echo $this->gridCountRespondentsAll($model->quiz_id); ?></b></span>
				</h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>

		<div class="form-group" id="choose_form">
			<?php  echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
				'name'=>"Groups respondents",
				'attr'=>'groupsRespondents',
				'list'=>CHtml::listData(GroupRespondents::model()->with('respondents')->findAll(array('condition'=>'client_id="'.$client.'"','index'=>'id')), 'id', 'title'),
			)); ?>

			<div class="col-sm-6" style="text-align: right;">
			    <?php $this->widget('bootstrap.widgets.TbButton', array(
			        'label'=> Yii::t('app','Use group of respondents'),
			        'htmlOptions'=>array(
			            'data-toggle'=>'modal',
			            'data-target'=>'#groupsRespondentsModal',
			            'class'=>'btn btn-primary nCButton',
			        ),
			    )); ?>
			</div>

			<div class="col-sm-6" style="text-align: left;">
			    <?php $this->widget('bootstrap.widgets.TbButton', array(
					'label' => Yii::t('app','Use base of respondents'),
					'htmlOptions'=>array(
						'id'=>'use_base',
						'class'=>'btn btn-primary nCButton',
                        'disabled'=>'disabled'
					),
			    )); ?>
			</div>
		</div>

        <div id="restrictionForm" style="display: none;">
		    <div class="groups-list">
		    	<ul><?php foreach ($model->groupsRespondents as $id => $elem): ?>
					<li id="TargetAudience_groupsRespondents_<?php echo ($id-1); ?>id"><?php echo $elem->title; ?></li>
				<?php endforeach; ?></ul>
		    </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'age',array('class'=>'col-sm-2 control-label')); ?>
				<div class="col-sm-6">
					<div class="col-sm-2">
						<?php echo $form->textField($model,'age_from',array('class'=>'form-control', 'style'=>'display: inline-block;')); ?>
					</div>
					<div class="col-sm-1">
						&#8212;
					</div>
					<div class="col-sm-2">
						<?php echo $form->textField($model,'age_to',array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-2"></div>
				<?php echo CHtml::tag('p', array('class'=>''), Yii::t('app', 'Put in both fields to «0» to ignore this parameter')); ?>
			</div>


			<div class="form-group">
				<?php echo $form->labelEx($model,'income',array('class'=>'col-sm-2 control-label')); ?>
				<div class="col-sm-6">
					<div class="col-sm-2">
						<?php echo $form->textField($model,'income_from',array('class'=>'form-control', 'style'=>'display: inline-block;')); ?>
					</div>
					<div class="col-sm-1">
						&#8212;
					</div>
					<div class="col-sm-2">
						<?php echo $form->textField($model,'income_to',array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-2"></div>
				<?php echo CHtml::tag('p', array('class'=>''), Yii::t('app', 'Put in both fields to «0» to ignore this parameter')); ?>
			</div>


	        <?php echo $form->dropDownListRow($model, 'gender', TargetAudience::itemAlias('GenderAudience')); ?>

	        <?php echo $form->dropDownListRow($model, 'marital_state', TargetAudience::itemAlias('MaritalStateAudience')); ?>

			<?php
				/*
					Vadim asked hide this field
					echo $form->dropDownListRow($model,'minimal_user_state_id', CHtml::listData(Status::model()->findAll(), 'id', 'title'));
				*/
				$model->minimal_user_state_id = 1;
				echo $form->hiddenField($model,'minimal_user_state_id');
			?>

		    <?php echo $form->textFieldRow($model,'count_limit',array('class'=>'form-control','hint'=>'Не действует, если не заполнено.')); ?>


			<div class="form-group">
				<div class="col-sm-2">
			        <?php echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
			            'name'=>"Educations",
			            'attr'=>'educations',
			            'list'=>CHtml::listData(DictEducation::model()->findAll(), 'dict_education_id', 'title'),
			        )); ?>
			  	</div>
			  	<div class="col-sm-2">
			        <?php echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
			            'name'=>"Scopes of activity",
			            'attr'=>'scopes',
			            'list'=>CHtml::listData(DictScope::model()->findAll(), 'dict_scope_id', 'title'),
			        )); ?>
			  	</div>
			  	<div class="col-sm-2">
			        <?php echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
			            'name'=>"Job positions",
			            'attr'=>'job_position',
			            'list'=>CHtml::listData(DictJobPosition::model()->findAll(), 'dict_job_position_id', 'title'),
			        )); ?>
			  	</div>
			  	<div class="col-sm-2">
			        <?php  echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
			            'name'=>"Countries and cities",
			            'attr'=>'countries',
			            'list'=>CHtml::listData(DictCountry::model()->findAll(), 'dict_country_id', 'title'),
			        )); ?>
			  	</div>
			  	<div class="col-sm-2">
			        <?php  echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
			            'name'=>"Classification answers",
			            'attr'=>'classfAnswers',
			            'list'=>Answer::model()->with('question', 'question.group', 'question.group.quiz')->findAll(array('condition'=>'question.is_class=1 AND quiz.state!="'.Quiz::STATE_EDIT.'" AND quiz.manager_id="'.Yii::app()->user->id.'"','index'=>'id')),
			        )); ?>
			  	</div>
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
		</div><!-- choosing form -->

	</div>
<?php $this->endWidget(); ?>

<?php
	if ($formtype == "update") {
		echo '
			<script>
				$(function() {
		';
		if ($model->groupsRespondents) {
			echo "$('#use_group').click();";
		}
		else {
			echo "$('#use_base').click();";
		}
		echo '
			});
			</script>
		';
	}
?>

<script>
    $(document).on('click','#groupsRespondentsModal a.close', function () {
        //location = "<?=$_SERVER['HTTP_REFERER'];?>"
    })
</script>
