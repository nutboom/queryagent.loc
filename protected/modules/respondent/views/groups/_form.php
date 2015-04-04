<?php Yii::app()->getClientScript()->registerScriptFile('/js/respondents.js'); ?>
<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'group-respondents-form',
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


        <?php $this->widget('bootstrap.widgets.TbAlert', array(
            'block'=>true, // display a larger alert block?
            'fade'=>true, // use transitions?
            'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
            'alerts'=>array( // configurations per alert type
                'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            ),
        )); ?>

		<?php echo $form->textFieldRow($model, 'title', array('class'=>'form-control', 'maxlength'=>255)); ?>

        <?php if(Yii::app()->user->isAdmin() || Yii::app()->user->isMarketer()): ?>
            <?php if(isset($clients[$model->client_id])): ?>
                <?php echo $form->radioButtonListRow($model, 'client_id', CHtml::listData($clients, 'id', 'name')); ?>
            <?php else: ?>
				<div class="form-group">
					<div class="col-sm-2"></div>
					<div class="col-sm-8">
            			<?php echo Yii::t('app', 'You dont have clients now'); ?>,
            			<a href="<?php echo $this->createUrl('/user/client/create', array("respondets" => true)); ?>"><?php echo Yii::t('app', 'question create'); ?></a>
            		</div>
            	</div>
			<?php endif; ?>
        <?php else: ?>
            <?php echo $form->hiddenField($model, 'client_id'); ?>
        <?php endif; ?>

        <?php  echo $this->renderPartial('_multiple', array('form'=>$form, 'model'=>$model,
            'name'=>"Respondents",
            'attr'=>'respondents',
            'list'=>CHtml::listData(Yii::app()->user->respondentsInGroups(), 'id', 'fullName'),
        )); ?>


		<div class="form-group">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<p class="muted offset2">
					<small>
						<?php echo RespondentModule::t("You can add existing users to a group") ?>
					</small>
				</p>
				<div class="clearfix"></div>

			    <?php $this->widget('bootstrap.widgets.TbButton', array(
			        'label'=>RespondentModule::t('Respondents'),
			        'htmlOptions'=>array(
			            'data-toggle'=>'modal',
			            'data-target'=>'#respondentsModal',
			            'class'=>'btn btn-primary nCButton',
			        ),
			    )); ?>
			    <div class="clearfix"></div>

				<p class="muted offset2">
					<small>
						<?php echo RespondentModule::t("and/or add new new members") ?>,
						<?php echo RespondentModule::t("by filling out a form in accordance with the pattern") ?>
					</small>
				</p>
				<div class="clearfix"></div>


				<p><small><strong><?php echo RespondentModule::t("Template") ?></strong></small></p>
				<blockquote class="text-info">
					<?php
						echo Yii::t('app', "Last Name").' '.Yii::t('app', "First Name").','.Yii::t('app', "Phone number").' (7xxxxxxxxxx)'.','.Yii::t('app', "E-Mail").';'
					?>
				</blockquote>
				<div class="clearfix"></div>


				<div id="import_window" style="display: none; overflow: auto; height: 300px; width: 1000px; margin-bottom:20px;">
					<table id="import_table"></table>
				</div>

				<button id="importlink" class="btn btn-primary" type="button"><?php echo RespondentModule::t("upload .csv, .txt, .xls, .xslx-files for fast import of respondents") ?></button>
				<div id="importbuttons" style="display: none;">
					<button id="importsave" class="btn btn-primary" type="button"><?php echo RespondentModule::t("apply respondents") ?></button>
					<button id="importcansel" class="btn btn-primary" type="button"><?php echo RespondentModule::t("cansel respondents") ?></button>
				</div>

				<div style="display: none;"><input type="file" id="importfile"></div>
				<div style="display: none;" id="importloading"><img src="/images/ajax-loader.gif"></div>
				<input type="hidden" id="importhidden" class="btn" data-toggle='modal' data-target='#import_window'>
				<div class="clearfix"></div>


				<div id="text_area_respondents">
				    <?php echo $form->textArea($model, 'textarea', array('class'=>'form-control', 'rows'=>10)); ?>
<!--				    <label>-->
<!--				    	<input type="checkbox" name="GroupRespondents[sender_sms]" id="sender_sms" value="1" data-cost="--><?php //echo Yii::app()->params['sendSmsCost']; ?><!--" data-balance="--><?php //echo User::model()->findByPk(Yii::app()->user->id)->balance; ?><!--" checked>-->
<!--				    	--><?php //echo RespondentModule::t("send sms messages") ?>
<!--				    </label>-->
<!--				    &nbsp;&nbsp;&nbsp;-->
				    <label>
				    	<input type="checkbox" name="GroupRespondents[sender_email]" id="sender_email" value="1" checked>
				    	<?php echo RespondentModule::t("send email messages") ?>
				    </label>
<!--				    <div id="cost_info"><i>--><?php //echo RespondentModule::t("the total cost will be displayed here mailing sms-messages") ?><!--</i></div>-->
<!--					--><?php
//						$this->widget('bootstrap.widgets.TbButton', array(
//							'buttonType'=>'link',
//							'type'=>'primary',
//							'url'=> $this->createUrl('/user/finance/pay'),
//							'label'=>"Пополнить баланс",
//							'htmlOptions' => array("style" => "display: none;", 'id'=>'button_pay',)
//						));
//					?>
				</div>
			</div>
		</div>



		<div class="form-group">
			<div class="col-sm-2">
				<?php
					if (isset($_GET['quiz'])) {
						$label = Yii::t('app','Create and return to '.$quiz->type);
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