<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Templates').' '.Yii::t('app', 'Templates of sms and emails');
$this->breadcrumbs=array(
    Yii::t('app','Templates') => array('/catalog'),
	Yii::t('app','Templates of sms and emails'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Create dict'),'url'=>array('create')),
);

Yii::app()->getClientScript()->registerScriptFile('/js/bb.js');
?>


<div class="group-border-dashed form-horizontal">
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app','Templates of sms and emails'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

        <?php $this->widget('bootstrap.widgets.TbAlert', array(
            'block'=>true, // display a larger alert block?
            'fade'=>true, // use transitions?
            'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
            'alerts'=>array( // configurations per alert type
                'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            ),
        )); ?>
        
		<form action="" method="post">
			<div class="form-group">
				<label class="col-sm-2"><?php echo Yii::t('app','Templates of smsnewgroup');; ?></label>
				<div class="col-sm-10">
					<div class="content-buttons" data-textarea="Template_smsnewgroup">
						<a href="#" data-tag="b" title="Полужирный"><img src="/images/bb/text_bold.png" /></a>
						<a href="#" data-tag="i" title="Курсив"><img src="/images/bb/text_italic.png" /></a></a>
						<a href="#" data-tag="u" title="Подчёркнутый"><img src="/images/bb/text_underline.png" /></a>
						<a href="#" data-tag="link" title="Вставка ссылки"><img src="/images/bb/text_link.png" /></a>
						
						<a href="#" data-tag="left" title="По левому краю"><img src="/images/bb/text_align_left.png" /></a>
						<a href="#" data-tag="center" title="По центру"><img src="/images/bb/text_align_center.png" /></a>
						<a href="#" data-tag="right" title="По правому краю"><img src="/images/bb/text_align_right.png" /></a>
						
					</div>

					<?php echo CHtml::textArea('Template[smsnewgroup]', SmsTpl::getTpl("smsnewgroup"), array('class'=>"form-control")); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2"><?php echo Yii::t('app','Templates of emailnewgroup');; ?></label>
				<div class="col-sm-10">
					<div class="content-buttons" data-textarea="Template_emailnewgroup">
						<a href="#" data-tag="b" title="Полужирный"><img src="/images/bb/text_bold.png" /></a>
						<a href="#" data-tag="i" title="Курсив"><img src="/images/bb/text_italic.png" /></a></a>
						<a href="#" data-tag="u" title="Подчёркнутый"><img src="/images/bb/text_underline.png" /></a>
						<a href="#" data-tag="link" title="Вставка ссылки"><img src="/images/bb/text_link.png" /></a>
						
						<a href="#" data-tag="left" title="По левому краю"><img src="/images/bb/text_align_left.png" /></a>
						<a href="#" data-tag="center" title="По центру"><img src="/images/bb/text_align_center.png" /></a>
						<a href="#" data-tag="right" title="По правому краю"><img src="/images/bb/text_align_right.png" /></a>
					</div>
					<?php echo CHtml::textArea('Template[emailnewgroup]', SmsTpl::getTpl("emailnewgroup"), array('class'=>"form-control")); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-2">
					<?php $this->widget('bootstrap.widgets.TbButton', array(
						'buttonType'=>'submit',
						'type'=>'primary',
						'label'=> Yii::t('app','Save'),
					)); ?>
				</div>
			</div>
		</form>
	</div>
</div>

