<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Finanse");

$breadcrumbs=array(
	UserModule::t("Finanse"),
);

$this->breadcrumbs=$breadcrumbs;

$this->menu=array(
	array('label'=>UserModule::t('Tariff Plan'),'url'=>array('/user/finance/index')),
	array('label'=>UserModule::t('Replenishment Balance'),'url'=>array('/user/finance/pay')),
	array('label'=>UserModule::t('Sub Users'),'url'=>array('/user/finance/sub')),
);
?>


<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<div class="group-border-dashed form-horizontal">
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Information of finance'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group"><div class="col-sm-8">
			<?php
				if ((int)$license->limits->cost) {
					echo Yii::t('app', 'Information expirate license', array(
						"{name}" => $license->limits->name,
						"{expirate}" => $license->date_expirate,
					));
				}
				else {
					echo Yii::t('app', 'Information about free license', array(
						"{name}" => $license->limits->name,
						"{expirate}" => $license->date_expirate,
					));
				}
			?>
		</div></div>

		<div class="form-group"><div class="col-sm-8">
			<?php
				echo Yii::t('app', 'Sum on balance, add', array(
					"{balance}" => $user->balance,
					"{link}" => $this->createUrl('/user/finance/pay'),
				));
			?>
		</div></div>

	</div>
</div>

<?php
	$tariffs	=	Tariffs::selectTariff($license->tariff);
	if ($tariffs):
?>
	<div class="col-sm-6 no-padding">
		<div class="group-border-dashed form-horizontal">
			<div class="step-pane">
				<div class="no-padding nCForm-group">
					<div class="col-sm-7">
						<h3 class="hthin"><?php echo Yii::t('app', 'Change tariff plan'); ?></h3>
					</div>
					<div class="clearfix"></div>
				</div>

				<?php
					$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'=>'quiz-form',
						'type'=>'horizontal',
						'htmlOptions'=>array('class'=>'group-border-dashed'),
						'enableAjaxValidation'=>false,
					));
				?>
					<?php echo $form->errorSummary($change); ?>

					<?php echo $form->dropDownListRow($change, 'tariff', $tariffs); ?>

					<div class="form-group">
						<div class="col-sm-2">
							<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType'=>'submit',
								'type'=>'primary',
								'label'=> Yii::t('app','Change'),
							)); ?>
						</div>
					</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
<?php
	endif;
?>

<?php
	$months	=	Tariffs::monthTariffs($license->tariff);
	if ($months):
?>
	<div class="col-sm-6 no-padding">
		<div class="group-border-dashed form-horizontal">
			<div class="step-pane">
				<div class="no-padding nCForm-group">
					<div class="col-sm-7">
						<h3 class="hthin"><?php echo Yii::t('app', 'Payments services'); ?></h3>
					</div>
					<div class="clearfix"></div>
				</div>

				<?php
					$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'=>'quiz-form',
						'type'=>'horizontal',
						'htmlOptions'=>array('class'=>'group-border-dashed'),
						'enableAjaxValidation'=>false,
					));
				?>
					<?php echo $form->errorSummary($extend); ?>

					<?php echo $form->dropDownListRow($extend, 'months', $months); ?>

					<div class="form-group">
						<div class="col-sm-2">
							<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType'=>'submit',
								'type'=>'primary',
								'label'=> Yii::t('app','Extend'),
							)); ?>
						</div>
					</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
<?php
	endif;
?>