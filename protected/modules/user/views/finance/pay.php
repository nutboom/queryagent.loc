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



if ($_GET['type'] == "success") {


}
?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<div class="group-border-dashed form-horizontal">
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin">
					<?php
						if ($paylicense) {
							echo Yii::t('app', 'Pay my license');
						}
						else {
							echo Yii::t('app', 'Pay my balance');
						}
					?></h3>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php if ($paylicense): ?>
			<div class="form-group">
				<div class="col-sm-2">

                    <?php
                    	$md5	=	"queryagent:".$model->summ.":".$model->id.":jUijx1nH14";
                    	$md5	=	md5($md5);
                    ?>

					<form action="https://merchant.roboxchange.com/Index.aspx" method="POST">
						<?php echo CHtml::hiddenField('MrchLogin', "queryagent"); ?>
						<?php echo CHtml::hiddenField('InvId', $model->id); ?>
						<?php echo CHtml::hiddenField('OutSum', $model->summ); ?>
						<?php echo CHtml::hiddenField('SignatureValue', $md5); ?>
						<?php echo CHtml::hiddenField('Desc', 'Informations'); ?>
						<?php echo CHtml::submitButton(Yii::t('app','Pay my license with {summ} rub', array("{summ}" => $model->summ)),array('class'=>'btn btn-primary')); ?>
					</form>
				</div>
			</div>
		<?php elseif ($model->id): ?>
			<div class="form-group">
				<div class="col-sm-12">
					<?php echo Yii::t('app','Order to add balance successfully created'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-2">
					<!--
					<form action="https://test.paysecure.ru/pay/order.cfm" method="POST">
						<?php echo CHtml::hiddenField('Merchant_ID', Yii::app()->params['assistMerchantId']); ?>
						<?php echo CHtml::hiddenField('OrderNumber', $model->id); ?>
						<?php echo CHtml::hiddenField('OrderAmount', $model->summ); ?>
						<?php echo CHtml::hiddenField('OrderComment', 'Informations'); ?>
						<?php echo CHtml::submitButton(Yii::t('app','Pay {summ} rub.', array("{summ}" => $model->summ)),array('class'=>'btn btn-primary')); ?>
					</form>
					-->
                    <?php
                    	$md5	=	"queryagent:".$model->summ.":".$model->id.":jUijx1nH14";
                    	$md5	=	md5($md5);
                    ?>


					<form action="https://merchant.roboxchange.com/Index.aspx" method="POST">
					<!-- <form action="http://test.robokassa.ru/Index.aspx" method="POST"> -->
						<?php echo CHtml::hiddenField('MrchLogin', "queryagent"); ?>
						<?php echo CHtml::hiddenField('InvId', $model->id); ?>
						<?php echo CHtml::hiddenField('OutSum', $model->summ); ?>
						<?php echo CHtml::hiddenField('SignatureValue', $md5); ?>
						<?php echo CHtml::hiddenField('Desc', 'Informations'); ?>
						<?php echo CHtml::submitButton(Yii::t('app','Pay {summ} rub.', array("{summ}" => $model->summ)),array('class'=>'btn btn-primary')); ?>
					</form>
				</div>
			</div>


		<?php else: ?>
			<?php
				$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
					'id'=>'pay-form',
					'type'=>'horizontal',
					'htmlOptions'=>array('class'=>'group-border-dashed'),
					'enableAjaxValidation'=>false,
				));
			?>

			<?php echo $form->errorSummary($model); ?>

			<div class="form-group">
				<div class="col-sm-8"></div>
				<?php echo $form->textFieldRow($model,'summ',array('class'=>'form-control')); ?>
			</div>

			<div class="form-group">
				<div class="col-sm-2">
					<?php $this->widget('bootstrap.widgets.TbButton', array(
						'buttonType'=>'submit',
						'type'=>'primary',
						'label'=>$model->isNewRecord ? Yii::t('app','Next') : Yii::t('app','Save'),
					)); ?>
				</div>
			</div>
			<?php $this->endWidget(); ?>
		<?php endif; ?>
	</div>
</div>


