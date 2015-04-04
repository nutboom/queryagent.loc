<?php
$this->pageTitle=Yii::app()->name . ' / '. UserModule::t('Users') . ' / '.UserModule::t('Clients user');
$this->breadcrumbs=array(
	UserModule::t('Users')=>array('/user'),
	UserModule::t('Clients user'),
);

$this->menu=array(
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
    array('label'=>Yii::t('app','List Client'), 'url'=>array('/user/client')),
);
?>
<h1><?php echo UserModule::t("Clients user").' '.$model->username; ?></h1>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
        'type'=>'horizontal',
        'htmlOptions'=>array('class'=>'well'),
	'enableAjaxValidation'=>false,
)); ?>

    <?php if(!$model->superuser && !$model->manager): ?>
        <?php echo CHtml::radioButtonList('User[client]',current(array_keys($model->client)),CHtml::listData(Client::model()->findAll(), 'id', 'name'),array('labelOptions'=>array('style'=>'display:inline'))); ?>
    <?php else: ?>
        <?php echo CHtml::checkBoxList('User[client]',array_keys($model->client),CHtml::listData(Client::model()->findAll(), 'id', 'name'),array('labelOptions'=>array('class'=>'inline_with_checkbox'))); ?>
    <?php endif; ?>

    <div class="row buttons">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'submit',
                    'type'=>'primary',
                    'label'=>UserModule::t('Save'),
            )); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->