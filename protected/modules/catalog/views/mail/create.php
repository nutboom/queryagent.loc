<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Job positions') . ' / '.Yii::t('app','Create dict');
$this->breadcrumbs=array(
	Yii::t('app','Mail templates')=>array('index'),
	Yii::t('app','Create'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app','Mail templates');
?>
<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>