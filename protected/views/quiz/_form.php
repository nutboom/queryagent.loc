<?php Yii::app()->getClientScript()->registerScriptFile('/js/quiz.js'); ?>

<?php
	Yii::app()->getClientScript()->registerScriptFile('/js/bb.js');
	Yii::app()->clientScript->registerScript('structure', "
    $(document).ready(function() {
    	$('#Quiz_state').change(function() {
        	if ($(this).val() == '".Quiz::STATE_REFUSE."') {
            	$('#quiz_refuse').show();
        	}
        	else {
        		$('#quiz_refuse').hide();
        	}
        });

		$('#Quiz_state').trigger('change');
    });
");
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
				<h3 class="hthin"><?php echo $action; ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>


		<?php echo $form->textFieldRow($model,'title',array('class'=>'form-control','maxlength'=>150)); ?>

		<div class="form-group">
			<label class="col-sm-2 control-label required" for="Quiz_description">
				<?php echo Yii::t('app', 'Client Name'); ?>
				<span class="required">*</span>
			</label>
			<div class="col-sm-8">
				<div class="col-sm-3">
			        <?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>
			            <div class="modal-header">
			                <a class="close" data-dismiss="modal">&times;</a>
			                <h4><?php echo Yii::t('app',"Choose Client"); ?></h4>
			            </div>

			            <div class="modal-body offset1">
			                <?php if(count($user->client)): ?>
			                    <?php echo $form->radioButtonList($model,'client_id', CHtml::listData($user->client, 'id', 'name'), array('class'=>'Quiz_elements')); ?>
			                <?php else: ?>
			                    <p><?php echo Yii::t('app',"No clients"); ?></p>
			                <?php endif; ?>
			                <!-- <br />
			                <a href="<?php //echo $this->createUrl('/user/client/create', array($model->type => ($model->quiz_id) ? $model->quiz_id : 0)); ?>"><?php //echo Yii::t('app',"Create Client"); ?></a> -->
			            </div>

			            <div class="modal-footer">
			                <?php if(count($user->client)) $this->widget('bootstrap.widgets.TbButton', array(
			                    'type'=>'primary',
			                    'label'=>Yii::t('app','Save'),
			                    'url'=>'#',
			                    'htmlOptions'=>array('data-dismiss'=>'modal'),
			                )); ?>
			            </div>
			        <?php $this->endWidget(); ?>

                    <?php $this->widget('bootstrap.widgets.TbButton', array(
                        'label'=>Yii::t('app',"Choose Client"),
                        'htmlOptions'=>array(
                        	'class'=>'btn btn-primary nCButton',
                            'data-toggle'=>'modal',
                            'data-target'=>'#myModal',
                        ),
                    )); ?>
				</div>

				<div class="col-sm-4" id="show_client">
    				<? if($model->client){ ?>
                        <p><? echo $model->client->name; ?></p>
                     <?}
                    else{
                        ?>
                        <?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'createCompModal')); ?>
                        <div class="modal-header">
                            <a class="close" data-dismiss="modal">&times;</a>
                            <h4>Создание новой компании</h4>
                        </div>

                        <div class="modal-body offset1">
                                <p>Название:</p><input type="text" class="form-control" id="CompName">
                        </div>

                        <div class="modal-footer">
                            <?php $this->widget('bootstrap.widgets.TbButton', array(
                                'type'=>'primary',
                                'label'=>Yii::t('app','Save'),
                                'url'=>'#',
                                'htmlOptions'=>array('data-dismiss'=>'modal',
                                    'id'=>'createCompModalButton'),
                            )); ?>
                        </div>
                        <?php $this->endWidget(); ?>
                        <script>
                            $(document).on('click','#createCompModalButton',function(){
                                var compName = $('#CompName').val();

                                $.ajax({
                                    url: "/user/client/ajaxcreate",
                                    type:"POST",
                                    dataType:"json",
                                    data:{compname:compName,quiz_id:<?echo ($model->quiz_id) ? $model->quiz_id : 0;?>}
                                })
                                    .done(function( data ) {
                                            var html = '<input type="hidden" value="'+data.client_id+'" name="Quiz[client_id]">'
                                            $("#show_client").html(html + data.compname);
                                    });


                            })
                        </script>

                        <?php $this->widget('bootstrap.widgets.TbButton', array(
                            'label'=>'Или создать новую',
                            'htmlOptions'=>array(
                                'class'=>'btn btn-primary nCButton',
                                'data-toggle'=>'modal',
                                'data-target'=>'#createCompModal',

                            ),
                        )); ?>
                    <?
                    }
                    ?>
				</div>
				<div class="col-sm-5">
					<div class="radio nCRight green">
						<?php echo CHtml::hiddenField('Quiz[anonymous_client]', '0'); ?>
						<label>
							<?php echo CHtml::checkBox('Quiz[anonymous_client]', $model->anonymous_client, array('class'=>'icheck right')); ?>
							<?php echo $attributes['anonymous_client']; ?>
						</label>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>

		<!-- <?php echo $form->textFieldRow($model,'fill_time',array('class'=>'form-control','maxlength'=>255)); ?> -->


		<div class="form-group">
			<label class="col-sm-2 control-label required" for="Quiz_description">
				<?php echo $model->getAttributeLabel('description'); ?>
				<span class="required">*</span>
			</label>
			<div class="col-sm-8">
				<div class="content-buttons" data-textarea="Quiz_description">
					<a href="#" data-tag="b" title="Полужирный"><img src="/images/bb/text_bold.png" /></a>
					<a href="#" data-tag="i" title="Курсив"><img src="/images/bb/text_italic.png" /></a></a>
					<a href="#" data-tag="u" title="Подчёркнутый"><img src="/images/bb/text_underline.png" /></a>
					<a href="#" data-tag="link" title="Вставка ссылки"><img src="/images/bb/text_link.png" /></a>
					
					<a href="#" data-tag="left" title="По левому краю"><img src="/images/bb/text_align_left.png" /></a>
					<a href="#" data-tag="center" title="По центру"><img src="/images/bb/text_align_center.png" /></a>
					<a href="#" data-tag="right" title="По правому краю"><img src="/images/bb/text_align_right.png" /></a>
					
				</div>
				<textarea id="Quiz_description" class="form-control" name="Quiz[description]" cols="50" rows="6"><?php echo $model->description; ?></textarea>
			</div>
		</div>


		<!--<?php echo $form->textFieldRow($model,'money',array('class'=>'form-control','maxlength'=>5)); ?>-->

		<?php /*echo Yii::t('app', 'Minimum cost of respondent is');*/ ?>

		<?php /* echo $form->textFieldRow($model,'karma',array('class'=>'form-control','maxlength'=>5)); */ ?> 

		<div class="form-group">
			<label class="col-sm-2 control-label"><?php echo $attributes['deadline']; ?></label>
            <div class="col-sm-8">
                <div class="col-sm-3" style="padding-left: 0">
                    <div class="date datetime input-group monthonly" data-link-field="dtp_input1">
                        <?php echo CHtml::textField('Quiz[deadline]', $model->deadline, array('class'=>"form-control")); ?>
                        <span class="input-group-addon btn btn-primary">
						<span class="glyphicon glyphicon-th"></span>
					</span>
                    </div>
                </div>
                <div class="col-sm-4"> </div>
                <div class="col-sm-5" id="skip_start_page_info">
                    <div class="radio green htooltip">
                    	<span>Если стоит галочка, то респондент, получив ссылку на опрос, сразу попадает на первый вопрос в опросе.<br><br>
                            Если галочка не стоит, то респондент, получив ссылку на опрос, попадает на стартовую страницу опроса с "Названием опроса", "Описанием опроса" и кнопкой "Пройти опрос".</span>
                        <?php echo CHtml::hiddenField('Quiz[skip_start_page]', '0'); ?>
                        <label>
                            <?php echo CHtml::checkBox('Quiz[skip_start_page]', $model->skip_start_page, array('class'=>'icheck right')); ?>
                            <?php echo $attributes['skip_start_page']; ?>
                        </label>
                    </div>
                </div>
            </div>


		</div>

		<?php if (isset($typeState)): ?>
			<?php echo $form->dropDownListRow($model, 'state', Quiz::itemAlias($typeState)); ?>
		<?php endif; ?>

		<div id="quiz_refuse" style="display: none;">
			<?php echo $form->textFieldRow($model,'refuse',array('class'=>'form-control')); ?>
		</div>

        <?php /*echo $form->checkboxRow($model, 'isSendMessenge');*/ ?>

        <?php echo $form->hiddenField($model, 'type'); ?>

		<? if (false){?>
		<div class="form-group">
			<div class="col-sm-10">
				<div class="radio nCRight green">
					<?php echo CHtml::hiddenField('Quiz[needs_confirmation]', '0'); ?>
					<label>
						<?php echo CHtml::checkBox('Quiz[needs_confirmation]', $model->needs_confirmation, array('class'=>'icheck right')); ?>
						<?php echo $attributes['needs_confirmation']; ?>
					</label>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="form-group">
            <div class="col-sm-10">
                <div class="radio nCRight green">
                	<span>Если стоит галочка, то респондент, получив ссылку на опрос, сразу попадает на первый вопрос в опросе.<br><br>
                            Если галочка не стоит, то респондент, получив ссылку на опрос, попадает на стартовую страницу опроса с "Названием опроса", "Описанием опроса" и кнопкой "Пройти опрос".</span>
                    <?php echo CHtml::hiddenField('Quiz[skip_start_page]', '0'); ?>
                    <label>
                        <?php echo CHtml::checkBox('Quiz[skip_start_page]', $model->skip_start_page, array('class'=>'icheck right')); ?>
                        <?php echo $attributes['skip_start_page']; ?>
                    </label>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <?}?>
		<?php if($model->state && $model->state == Quiz::STATE_FILL): ?>
			<div class="form-group">
				<div class="col-sm-10">
					<div class="radio nCRight green">
						<?php echo CHtml::hiddenField('Quiz[archive]', '0'); ?>
						<label>
							<?php echo CHtml::checkBox('Quiz[archive]', $model->archive, array('class'=>'icheck right')); ?>
							<?php echo $attributes['archive']; ?>
						</label>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
        <?php endif; ?>

		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>$model->isNewRecord ? Yii::t('app','Next') : Yii::t('app','Save'),
				)); ?>

				<?php if (($model->state == Quiz::STATE_EDIT ||$model->state == Quiz::STATE_FILL && $model->archive) && $model->quiz_id): ?>
					<?php $this->widget('bootstrap.widgets.TbButton', array(
						'url'=>array('/'.$model->type.'/delete/'.$model->quiz_id),
						'type'=>'warning',
						'label'=> Yii::t('app','Delete'),
					)); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>