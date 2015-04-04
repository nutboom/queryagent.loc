<?php
/* @var $this GroupRespondentsController */
/* @var $model GroupRespondents */
$this->pageTitle=Yii::app()->name . ' / '. Yii::app()->getModule('respondent')->t('Groups respondents') . ' / '.Yii::t('app', 'Create');
$this->breadcrumbs=array(
	Yii::app()->getModule('respondent')->t('Groups respondents')=>array('index'),
	Yii::t('app', 'Create'),
);

$this->menu=array(
	array('label'=>Yii::app()->getModule('respondent')->t('List groups'), 'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app', 'Create').' '.Yii::app()->getModule('respondent')->t('Groups respondents');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'quiz'=>$quiz, 'clients'=>$clients, 'action'=>$action)); ?>