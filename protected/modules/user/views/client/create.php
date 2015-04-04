<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app', 'Clients') . ' / '.UserModule::t('Create');
$this->breadcrumbs=array(
	Yii::t('app', 'Clients')=>array('index'),
	UserModule::t('Create'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List Client'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app','Create Client');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'quiz'=>$quiz,'respondets'=>$respondets,'action'=>$action)); ?>