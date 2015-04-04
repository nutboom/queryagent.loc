<?php Yii::app()->getClientScript()->registerScriptFile('/js/structureQuiz.js'); ?>
<?php Yii::app()->clientScript->registerScript('structure', "
    $(document).ready(function() {
        $('#quiz_structure').jqDynaForm('set', ".$model.");
        setPreView(".$model.");
    });
"); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'preView')); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('app',"Pre view quiz"); ?></h4>
	</div>

	<div class="modal-body" id="preview_quiz">

	</div>
<?php $this->endWidget(); ?>

<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'structure-quiz-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed','enctype' => 'multipart/form-data'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane <?php echo $quiz->type?>__class">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Content template '.$quiz->type); ?>: «<?php echo $quiz->title; ?>»</h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php echo $form->errorSummary($question, $quiz); ?>
		<div id="error-message" class="alert alert-block alert-error">
		    <p><?php echo Yii::t('yii','Please fix the following input errors:'); ?></p>
		    <ul></ul>
		</div>

		<?php echo $form->textFieldRow($quiz,'title',array('class'=>'form-control','maxlength'=>150)); ?>

		<div id="quiz_structure">
		    <div class="pageWrapper" data-holder-for="groups"></div>
		</div>

		<div style="text-align: right; margin-right: 20px;">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'label'=>Yii::t('app',"Pre view quiz"),
					'htmlOptions'=>array(
						'class'=>'btn btn-primary nCButton',
						'data-toggle'=>'modal',
						'data-target'=>'#preView',
					),
				)); ?>
		</div>

		<div class="form-group">
			<?php if($quiz->type != 'mission'):?>
			<div class="col-sm-2">
			    <?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType' => 'submit',
					'icon' => 'plus white',
					'type' => 'primary',
					'label' => Yii::t('app','Add block questions'),
					'htmlOptions'=>array('class'=>'btn-addPage')
			    )); ?>
			</div>
			<?php endif; ?>
			<div class="col-sm-5">

			</div>

			<div class="buttons__wrapper" style="text-align: right;">
			    <?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType' => 'submit',
					'icon' => 'icon-ok icon-white',
					'type' => 'danger',
					'label' => Yii::t('app','Save'),
					'htmlOptions'=>array('name'=>'justSave')
			    )); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

    <!-- Subforms library -->
    <div style="display:none" id="subforms_library">
        <div data-name="groups" data-label="Groups" class="groups" data-number-group="number_groups" data-number-question="number_question">
            <fieldset>
                <?php echo CHtml::hiddenField('GroupQuestions[number_groups][id]'); ?>
                <?php echo CHtml::hiddenField('GroupQuestions[number_groups][groups_orderby]','number_groups'); ?>

                <?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'conditionsModal_number_groups')); ?>
                    <div class="modal-header">
                        <a class="close" data-dismiss="modal">&times;</a>
                        <h4><?php echo Yii::t('app', 'Display conditions'); ?></h4>
                    </div>

                    <div class="modal-body"></div>
                <?php $this->endWidget(); ?>
                <legend class="pagelegend">
                    <em><?php echo Yii::t('app', 'Block questions'); ?>&nbsp;<span class="number_groups"></span></em>
                    <a href="javascript: void(0);" data-toggle='modal' data-target='#conditionsModal_number_groups' class='conditions'><?php echo Yii::t('app', 'Display conditions');?> </a>

                </legend>
                <div class="questionWrapper">
		    		<div data-holder-for="question"></div>
		   			<div class="form-group">
		    			<div class="col-sm-2">
							<button class="btn btn-primary addQuestion addLinq nCRight"><b class="fa fa-plus"></b>&nbsp;&nbsp;<?php echo Yii::t('app', 'Add item of '.$quiz->type); ?></button>
		    			</div>
		    		</div>
				</div>
            </fieldset>
        </div>

        <div data-name="question" data-label="Questions" class="question" data-number-group="number_groups" data-number-question="number_question">
            <legend>
                <div class="col-sm-2 answersHeader"><em><?php echo Yii::t('app', 'Question of '.$quiz->type); ?>&nbsp;<span class="number_question"></span></em></div>
            </legend>
            <div class="clearfix"></div>
            <fieldset>
                <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][id]'); ?>
                <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][question_orderby]','number_question'); ?>

				<div class="form-group">
				    <label class="col-sm-2 control-label"><?php echo Yii::t('app', 'Text question of '.$quiz->type); ?><span class="required">*</span></label>
				    <div class="col-sm-8">
					<?php echo CHtml::textField('GroupQuestions[number_groups][Question][number_question][text]','',array('class'=>'form-control',"placeholder" => Yii::t('app', 'Text question of '.$quiz->type))); ?>
				    </div>
				</div>


                <div class="form-group">
					<label class="col-sm-2 control-label"><?php echo Yii::t('app', 'Type question of '.$quiz->type); ?><span class="required">*</span></label>

                    <div class="col-sm-5 type_classf_question">
                        <?php
                        	$typeQuestions = ($quiz->type == "quiz") ? "quizTypes" : "missionTypes";
                        	echo CHtml::dropDownList('GroupQuestions[number_groups][Question][number_question][type]','', Question::itemAlias($typeQuestions), array( 'class'=>'Question_type'));
                        ?>
					 </div>

					<?php
					    if (User::model()->findByPk(Yii::app()->user->id)->license[0]->limits->limit_class == "yes") {
					?>
					    <label class="col-sm-3 control-label">
					    	<div class="radio nCRight green">
							<?php echo CHtml::checkBox('GroupQuestions[number_groups][Question][number_question][is_class]', null, array('class'=>'icheck right')) ?>
							<?php echo Yii::t('app', 'Classifying question'); ?>
							</div>
						</label>
					<?php
					    }
					?>
                </div>

                <div class="scale_question nCHidden">
                    <div class="form-group">
							<label class="col-sm-2 control-label"><?php echo Yii::t('app', 'Scaled Size'); ?><span class="required">*</span></label>
							<div class="col-sm-8">
                            <?php echo CHtml::textField('GroupQuestions[number_groups][Question][number_question][scaled_size]','',array('class'=>'form-control',"placeholder" => Yii::t('app', 'Scaled Size'))); ?>
                        </div>
                    </div>
                </div>



                <div class="form-group ">
                    <div class="controls answers_question">
                        <legend>
                            <div class="col-sm-2 answersHeader"><em><?php echo Yii::t('app', 'Answers'); ?></em></div>
                            <div class="clearfix"></div>
                        </legend>
                        <div class="answer-container" data-holder-for="answer"></div>
						<div class='col-sm-2'></div>
						<div class="col-sm-3"><a href="#" class="addAnswer addLinq"><button class="btn btn-primary"><b class="fa fa-plus"></b>&nbsp;&nbsp;Добавить ответ</button></a></div>
                    </div>
                </div>

               	<div class="form-group images__wrapper">
				    <legend>
					<div class="col-sm-2 answersHeader">
					    <em><?php echo Yii::t('app', 'Images'); ?></em>
					</div>
				    </legend>
				    <div class="col-sm-7">
					<div class="controls images_question">
					    <div data-holder-for="picture"></div>
					</div>
					<button class="btn btn-primary btn-add-image" onclick="addImageButton(this); return false;" style="margin: 0px;"><b class="fa fa-plus addImage"></b>&nbsp;&nbsp; Добавить изображение</button>
				    </div>
                </div>
            </fieldset>
        </div>

        <div data-name="picture" data-label="Pictures" class="picture" data-number-group="number_groups" data-number-question="number_question" data-number-image="number_image">
            <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][image][number_image][id]'); ?>
            <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][image][number_image][picture_orderby]','number_image'); ?>
            <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][image][number_image][link]','number_image'); ?>
            <?php echo CHtml::fileField('GroupQuestions[number_groups][Question][number_question][image][number_image][image]','',array('class'=>'hide picture_file')); ?>
            <div class="pictures_question empty_img">
                <img src="" alt="" />
            </div>
        </div>

        <div data-name="answer" data-label="Answers" class="answer" data-number-group="number_groups" data-number-question="number_question" data-number-answer="number_answer">
            <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][answer][number_answer][id]'); ?>
            <?php echo CHtml::hiddenField('GroupQuestions[number_groups][Question][number_question][answer][number_answer][answer_orderby]','number_answer'); ?>

	    <div class="form-group" style="margin: 0px; padding: 0px;">
		<label class="col-sm-2 control-label"><span class="number"><?php echo 'number_answer)'; ?></span></label>
		<div class="col-sm-4">
		     <?php echo CHtml::textField('GroupQuestions[number_groups][Question][number_question][answer][number_answer][text]','',array('class'=>'form-control',"placeholder" => Yii::t('app', 'Text answer'))); ?>
		</div>
		<div class="col-sm-1 scale_answer_wrapper nCHidden">
		    <?php echo CHtml::textField('GroupQuestions[number_groups][Question][number_question][answer][number_answer][orderby]','',array('class'=>'form-control scale_answer_question',"placeholder" => Yii::t('app', 'Scaled answer'))); ?>
		</div>
		<div class="col-sm-2">
		    <button class="btn btn-default btn-remove"><b class="fa fa-trash-o"></b></button>
		    <button class="btn btn-default btn-add"><b class="fa fa-plus"></b></button>
		</div>
	    </div>

    </div>
    </div>

    <!-- Modal -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'modalRemoteConditions')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><?php echo Yii::t('app', 'Attention'); ?></h4>
    </div>

    <div class="modal-body">
        <p><?php echo Yii::t('app', 'After moving the group display conditions for all groups will be abolished. If need be, write them again.'); ?></p>
    </div>

    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>Yii::t('app', 'OK'),
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
    </div>
<?php $this->endWidget(); ?>