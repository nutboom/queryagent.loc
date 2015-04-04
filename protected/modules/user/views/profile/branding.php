<?php
$this->pageTitle=Yii::app()->name . ' - '.Yii::t("app", "Branding");

$breadcrumbs=array(
	Yii::t("app", "Branding"),
);
$this->breadcrumbs=$breadcrumbs;

?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<?php
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'profile-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed','enctype' => 'multipart/form-data'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Branding'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary($model); ?>


		<div class="form-group">
		    <div class="col-sm-2">
				<img src="<?php echo $model->logo; ?>" />
		    </div>
		    <div class="col-sm-2 avatarElement">
				<button class="btn btn-primary loadAvatar"><?php echo Yii::t('app', 'Attach logo'); ?></button>
				<?php echo CHtml::fileField('logo','',array('class'=>'hidden')); ?>
				<?php echo Yii::t('app', 'PNG-file 100px x 40px'); ?>
		    </div>
		    <div class="col-sm-3 avatarElement">
				<span class="avatarAfter"><?php echo Yii::t('app', 'File attached'); ?>: <span class="filename">filename</span></span>
		    </div>
		</div>

        <div class="form-group">
            <div class="col-sm-2">
                <img src="<?php echo $model->logo_social; ?>" />
            </div>
            <div class="col-sm-2 avatarElement">
                <button class="btn btn-primary loadAvatar">Файл логотипа для соц. сетей</button>
                <?php echo CHtml::fileField('logo_social','',array('class'=>'hidden')); ?><br>
                PNG-файл 200px x 200px
            </div>
            <div class="col-sm-3 avatarElement">
                <span class="avatarAfter"><?php echo Yii::t('app', 'File attached'); ?>: <span class="filename">filename</span></span>
            </div>
        </div>


		<div class="form-group">
		    <div class="col-sm-2">
				<?php echo Yii::t('app', 'Top color branding #'); ?>
		    </div>
		    <div class="col-sm-4">
				<input type="color" name="Branding[top_color]" value="<?php echo Branding::getTopColor(); ?>">
		    </div>
		</div>

		<div class="form-group">
		    <div class="col-sm-2">
				<?php echo Yii::t('app', 'Left color branding #'); ?>
		    </div>
		    <div class="col-sm-4">
				<input type="color" name="Branding[left_color]" value="<?php echo Branding::getLeftColor(); ?>">
		    </div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=> Yii::t('app', 'Change'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

