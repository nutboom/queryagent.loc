<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Transactions');
$this->breadcrumbs=array(
    Yii::t('app','Transactions') => array("/licenses/transactions"),
    Yii::t('app','Edit'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List transactions'),'url'=>array('index')),
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>
<?php
	$action = Yii::t('app','Update transaction');
?>

<?php echo $this->renderPartial('_form',array('model'=>$model,'action'=>$action)); ?>