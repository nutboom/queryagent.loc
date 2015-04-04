<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Check questions and answers') . ' / '.Yii::t('app','Update dict');
$this->breadcrumbs=array(
	Yii::t('app','Dict').' '.Yii::t('app','Check questions and answers')=>array('index'),
	Yii::t('app','Update dict'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List dict'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app', 'Update dict');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'modelAnswer'=>$modelAnswer,'jsonModelAnswer'=>$jsonModelAnswer,'action'=>$action)); ?>