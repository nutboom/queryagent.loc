<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app','Tariffs') . ' / '.Yii::t('app','Create tariff');
$this->breadcrumbs=array(
	Yii::t('app','Tariffs')=>array('index'),
	Yii::t('app','Create tariff'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List tariffs'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app','Create tariff');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>