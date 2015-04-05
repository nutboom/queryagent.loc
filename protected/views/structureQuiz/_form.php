<?php Yii::app()->getClientScript()->registerScriptFile('/js/structureQuiz.js'); ?>
<?php Yii::app()->clientScript->registerScript('structure', "
    $(document).ready(function() {
        $('#quiz_structure').jqDynaForm('set', ".$model.");
        setPreView(".$model.");
    });
"); ?>

<?php
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'preView')); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('app',"Pre view quiz"); ?></h4>
	</div>

	<div class="modal-body" id="preview_quiz">

	</div>
<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'reqQuest')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Запрос на разработку анкеты</h4>
    </div>
<form action="/user/finance/requestForQuestionnaire" method="post">
    <input type="hidden" name="quiz_id" value="<?=$quiz->quiz_id?>">
    <div class="modal-body" id="reqQuestBody">
        <span>Сформулируйте в свободной форме вашу бизнес задачу, которую Вы хотите решить с помощью исследования и наши специалисты подготовят для Вас анкету опроса.
        Стоимость разработки анкеты - 5000 руб.</span><br>
        <textarea rows="5" style="width: 100%" name="reqQuestDescription"></textarea>
    </div>
    <button class="btn btn-primary" style="margin: 15px;" type="submit" name="saveAndRedirect">Отправить запрос</button>
</form>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'saveTemplate')); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('app',"Save as template"); ?></h4>
	</div>

	<div class="modal-body">
		<form action="" method="post">
			Название шаблона:<br />
			<input type="text" name="Templates[title]" class="form-control">
			<br>
			<button class="btn btn-primary" type="submit" name="saveAndRedirect"><?php echo Yii::t('app', 'Save'); ?></button>
		</form>
	</div>
<?php $this->endWidget(); ?>


<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'useTemplate')); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('app',"Choose Template"); ?></h4>
	</div>

	<?php
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'=>'quiz-form',
			'type'=>'horizontal',
			#'action'=>array('/'.$type.'/clone'),
		));
	?>
	<div class="modal-body offset1">
		<?php
			$quizs = Templates::model()->findAll("manager_id = :manager_id AND type = :type AND is_deleted = :deleted", array(":manager_id" => Yii::app()->user->id, ":type"=>$quiz->type, ":deleted"=>Templates::NO_DELETED));

			if(count($quizs) > 0) {
				echo CHtml::radioButtonList("template_id", '', CHtml::listData($quizs, 'id', 'title'), array('class'=>'Quiz_elements'));
				echo '
					<br />
					<a href="/catalog/templates" target="_new">Создать новый шаблон</a>
				';
			}
			else {
				echo Yii::t('app',"No template for use");
			}
		?>
	</div>

	<div class="modal-footer">
		<?php
			if(count($quizs) > 0) {
				$this->widget('bootstrap.widgets.TbButton', array(
				'type'=>'primary',
				'buttonType'=>'submit',
				'label'=>Yii::t('app','Create'),
				));
			}
		?>
	</div>
	<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>
<div id="reqQuest-wrapper">
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type' => 'primary',
    'label' => 'Оставить завявку на разработку анкеты',
    'htmlOptions'=>array(
        'class'=>'btn btn-primary',
        'data-toggle'=>'modal',
        'data-target'=>'#reqQuest',
    ),
)); ?>
</div>
<div style="clear:both;"></div>
<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'structure-quiz-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed','enctype' => 'multipart/form-data'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane <?php echo $quiz->type?>__class">
		<div class="no-padding nCForm-group" style="margin: 0;">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Content '.$quiz->type); ?>: «<?php echo $quiz->title; ?>»</h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div style="text-align: right; margin-right: 10px; margin-top: 10px;">
			<a href="#" data-toggle='modal' data-target='#useTemplate'>Использовать шаблон</a>
		</div>

		<?php
        if ($form->errorSummary($question) != '')
        {
            echo $form->errorSummary($question);
        ?>
		<div id="error-message" class="alert alert-block alert-error">
		    <p><?php echo Yii::t('yii','Please fix the following input errors:'); ?></p>
		    <ul></ul>
		</div>
        <?
        }
         ?>

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
			<div class="col-sm-2">
			    
			</div>

			<div class="col-sm-3">

			</div>

			<div class="buttons__wrapper" style="text-align: right;">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType' => 'submit',
					'type' => 'primary',
					'label' => Yii::t('app','Save as template'),
					'htmlOptions'=>array(
                        'class'=>'btn btn-primary',
                        'data-toggle'=>'modal',
                        'data-target'=>'#saveTemplate',
                    ),
			    )); ?>
			    
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'link',
					'type'=>'primary',
					'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
					'label'=>Yii::t('app','Back'),
				)); ?>

			    <?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType' => 'submit',
					'icon' => 'icon-ok icon-white',
					'type' => 'danger',
					'label' => Yii::t('app','Save'),
					'htmlOptions'=>array('name'=>'justSave')
			    )); ?>

				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>Yii::t('app','Next'),
					'htmlOptions'=>array('name'=>'saveAndRedirect')
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


					    <label class="col-sm-3 control-label">
                            <?php
                            //if (User::model()->findByPk(Yii::app()->user->id)->license[0]->limits->limit_class == "yes") {
                            if(false){
                            ?>
                                <div class="radio nCRight green" style="width: 255px;text-align: left;">
                                <?php echo CHtml::checkBox('GroupQuestions[number_groups][Question][number_question][is_class]', null, array('class'=>'icheck right')) ?>
                                <?php echo Yii::t('app', 'Classifying question'); ?>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="radio nCRight green" style="width: 255px;text-align: left;">
                                <?php echo CHtml::checkBox('GroupQuestions[number_groups][Question][number_question][is_not_required]', null, array('class'=>'icheck right')) ?>
                                Необязательный для ответа
                            </div>
						</label>

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