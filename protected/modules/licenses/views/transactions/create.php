<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Transactions');
$this->breadcrumbs=array(
    Yii::t('app','Transactions') => array("/licenses/transactions"),
    Yii::t('app','Create'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List transactions'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app','Create transaction');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>