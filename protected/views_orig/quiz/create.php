<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app','Create');

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Creation') => array(array('/'.$model->type.'/create'), "active"),
    Yii::t('app', 'Wizard Questions'),
    Yii::t('app', 'Wizard Audience'),

	Yii::t('app', 'Wizard Collection'),
	Yii::t('app', 'Wizard Launch'),
);
/*
$this->menu=array(
	array('label'=>Yii::t('app', ucfirst($model->type).'s'),'url'=>array('/'.$model->type)),
);*/
?>

<?php
	$action = Yii::t('app', 'Create '.$model->type);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'user'=>$user,'action'=>$action)); ?>