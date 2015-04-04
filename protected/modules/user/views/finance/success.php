<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Finanse");

$breadcrumbs=array(
	UserModule::t("Finanse") => array('/user/finance'),
	UserModule::t("Replenishment Balance"),
);

$this->breadcrumbs=$breadcrumbs;

$this->menu=array(
	array('label'=>UserModule::t('Tariff Plan'),'url'=>array('/user/finance/index')),
	array('label'=>UserModule::t('Replenishment Balance'),'url'=>array('/user/finance/pay')),
	array('label'=>UserModule::t('Sub Users'),'url'=>array('/user/finance/sub')),
);
?>


<div class="group-border-dashed form-horizontal">
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin">
					<?php echo Yii::t('app', 'Thank you you pay activated'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'link',
					'type'=>'primary',
					'url'=>array('/quiz/precreate'),
					'label'=>Yii::t('app','Create quiz'),
				)); ?>
			</div>
		</div>

	</div>
</div>


