<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app', 'Update '.$model->type);

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$model->type.'/'.$model->quiz_id.'/update'),
    Yii::t('app', 'Wizard Questions') => array('/'.$model->type.'/'.$model->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Audience') => array('/'.$model->type.'/'.$model->quiz_id.'/targetAudience'),
	Yii::t('app', 'Wizard Collection') => array(array('/'.$model->type.'/'.$model->quiz_id.'/collection'), "active"),
	Yii::t('app', 'Wizard Launch') => array('/'.$model->type.'/'.$model->quiz_id.'/launch'),
);

if ($model->state != Quiz::STATE_EDIT) {
	$this->menu=array(
		array('label'=>Yii::t('app', 'Results'),'url'=>array($model->type."/".$model->quiz_id."/Applications")),
		array('label'=>Yii::t('app', 'Statistics'),'url'=>array($model->type."/".$model->quiz_id."/statistics")),
		array('label'=>Yii::t('app', 'Comments'),'url'=>array($model->type."/".$model->quiz_id."/comments")),
		array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($model->type."/".$model->quiz_id."/export")),
	);
}

Yii::app()->getClientScript()->registerScriptFile('/js/bb.js');
Yii::app()->getClientScript()->registerScriptFile('/js/jquery.zclip.js');
Yii::app()->clientScript->registerScript('structure', "
    $(document).ready(function() {
		$('a#copylink').zclip({
			path:'/images/clipboard.swf',
			copy: $('#textlink').val(),
			afterCopy: function() {
				alert('Скопировано');
			}
		});

		$('a#copyframe').zclip({
			path:'/images/clipboard.swf',
			copy: $('#textframe').val(),
			afterCopy: function() {
				alert('Скопировано');
			}
		});
		$('a#copyshortlink').zclip({
			path:'/images/clipboard.swf',
			copy: $('#short_text').text(),
			afterCopy: function() {
				alert('Скопировано');
			}
		});
    });
", CClientScript::POS_END);
?>
<?php if(Yii::app()->user->hasFlash('pay_suc')):?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
  		<strong><?php echo Yii::app()->user->getFlash('pay_suc'); ?></strong>
	</div>
<?php endif; ?>


<?php
	$attributes = $model->attributeLabels();
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'quiz-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'optMail')); ?>
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h4><?php echo Yii::t('app', "Options for email sending"); ?></h4>
		</div>

		<div class="modal-body offset1">
			<a href="<?php echo $this->createUrl('/respondent/groups/create', array('quiz' => $model->quiz_id)); ?>"><?php echo Yii::t('app','Create Group of Respondents'); ?></a>
			<br /><br />
			
			<div class="content-buttons" data-textarea="mail_template">
				<a href="#" data-tag="b" title="Полужирный"><img src="/images/bb/text_bold.png" /></a>
				<a href="#" data-tag="i" title="Курсив"><img src="/images/bb/text_italic.png" /></a></a>
				<a href="#" data-tag="u" title="Подчёркнутый"><img src="/images/bb/text_underline.png" /></a>
				<a href="#" data-tag="link" title="Вставка ссылки"><img src="/images/bb/text_link.png" /></a>
						
				<a href="#" data-tag="left" title="По левому краю"><img src="/images/bb/text_align_left.png" /></a>
				<a href="#" data-tag="center" title="По центру"><img src="/images/bb/text_align_center.png" /></a>
				<a href="#" data-tag="right" title="По правому краю"><img src="/images/bb/text_align_right.png" /></a>
			</div>
			<textarea id="mail_template" class="form-control" name="Quiz[mail_template]" cols="50" rows="6"><?php echo $model->mail_template ? $model->mail_template : Yii::t('app', 'Email sender quiz template'); ?></textarea>
			<br />
			Теги для вставки: <b>[FirstName]</b> <b>[LastName]</b>
		</div>

		<div class="modal-footer">
			<?php $this->widget('bootstrap.widgets.TbButton', array(
				'type'=>'primary',
				'label'=>Yii::t('app', 'Close'),
				'url'=>'#',
				'htmlOptions'=>array('data-dismiss'=>'modal'),
			)); ?>
		</div>
	<?php $this->endWidget(); ?>

	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Wizard Collection'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php echo $form->errorSummary($model); ?>

		<?if (false){?>
		<div class="form-group">
			<div class="col-sm-1" style="width:55px;">
				<div class="col-sm-2">
					<div class="radio green">
						<?php echo CHtml::checkBox('Quiz[tester]', "1", array('class'=>'icheck right', 'disabled'=>'disabled')); ?>
					</div>
				</div>
			</div>
			<div class="col-sm-7">
				<label class="col-sm-12 control-label nCLeft-text"><?php echo Yii::t('app', 'Mobile Application'); ?></label>
			</div>
		</div>
		<?}?>
		<div class="form-group">
			<div class="col-sm-1" style="width:55px;">
				<div class="col-sm-2">
					<div class="radio green">
						<?php echo CHtml::checkBox('Quiz[tester]', "1", array('class'=>'icheck right', 'disabled'=>'disabled')); ?>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<label class="col-sm-12 control-label nCLeft-text">Полная ссылка</label>
			</div>

			<div class="col-sm-7">
				<span id="textlink" style="width: 90%; margin-right: 20px; float: left;">http://panel.queryagent.ru/?h=<?php echo $model->hash; ?></span>
				<a width=16 height=16 href="#" id="copylink" title="<?php echo Yii::t('app', 'Copy to buffer'); ?>"><img src="/images/page_copy.png"></a>
				<a width=16 height=16 href="http://panel.queryagent.ru/?h=<?php echo $model->hash; ?>" target="_blank" title="<?php echo Yii::t('app', 'Open in new window'); ?>"><img src="/images/page_go.png"></a>

			</div>
		</div>
        <div class="form-group">
            <div class="col-sm-1" style="width:55px;">
                <div class="col-sm-2">
                    <div class="radio green">
                        <?php echo CHtml::checkBox('Quiz[tester]', "1", array('class'=>'icheck right', 'disabled'=>'disabled')); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <label class="col-sm-12 control-label nCLeft-text">Короткая ссылка</label>
            </div>
            <div class="col-sm-7">
                <?
                $bitly = Yii::app()->mybitly->getShorten("http://panel.queryagent.ru/?h=".$model->hash);

                if($bitly['status_code'] == 200 && $bitly['status_txt'] == 'OK')
                {
                    $short_ly = $bitly['data']['url'];

                    echo "<span  style=\"width: 90%; margin-right: 20px; float: left;\" id='short_text'>".$short_ly."</span>";
                    ?>
                    <a width=16 height=16 href="#" id="copyshortlink" title="<?php echo Yii::t('app', 'Copy to buffer'); ?>"><img src="/images/page_copy.png"></a>
                    <a width=16 height=16 href="<?php echo $short_ly; ?>" target="_blank" title="<?php echo Yii::t('app', 'Open in new window'); ?>"><img src="/images/page_go.png"></a>
                <?
                }
                else echo "<div>Не удалось получить короткую ссылку</div>";
                ?>
            </div>

        </div>



		<div class="form-group">
			<div class="col-sm-1" style="width:55px;">
				<div class="col-sm-2">
					<div class="radio green">
						<?php echo CHtml::checkBox('Quiz[tester]', "1", array('class'=>'icheck right', 'disabled'=>'disabled')); ?>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<label class="col-sm-12 control-label nCLeft-text">Код для вставки виджета опроса на сайт</label>
			</div>

			<div class="col-sm-7">
				<textarea id="textframe" class="form-control" cols="50" rows="6"><iframe src="http://panel.queryagent.ru/?h=<?php echo $model->hash; ?>" style="width: 800px; height: 600px;"></iframe></textarea>
				<a href="#" id="copyframe">скопировать код в буфер</a>
			</div>
		</div>


		<?php echo CHtml::hiddenField('Quiz[is_mailsender]', '0'); ?>
		<?if (count($model->audience) > 0){?>
		<div class="form-group">
			<div class="col-sm-1" style="width:55px;">
				<div class="col-sm-2">
					<div class="radio green">
						<?php echo CHtml::hiddenField('Quiz[is_mailsender]', '0'); ?>
						<?php echo CHtml::checkBox('Quiz[is_mailsender]', $model->is_mailsender, array('class'=>'icheck right')); ?>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<label class="col-sm-12 control-label nCLeft-text"><?php echo Yii::t('app', 'Email sender quiz'); ?></label>
			</div>

			<div class="col-sm-7">
				<a href="#" data-toggle="modal" data-target="#optMail"><?php echo Yii::t('app', "Options for email sending"); ?></a>

			</div>
		</div>
		<?}?>


	</div>





		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'link',
					'type'=>'primary',
					'url'=>array('/'.$model->type.'/'.$model->quiz_id.'/TargetAudience'),
					'label'=>Yii::t('app','Back'),
				)); ?>

				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>Yii::t('app','Next'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>