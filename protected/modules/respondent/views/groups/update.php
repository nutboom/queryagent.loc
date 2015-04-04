<?php
/* @var $this GroupRespondentsController */
/* @var $model GroupRespondents */
$this->pageTitle=Yii::app()->name . ' / '. Yii::app()->getModule('respondent')->t('Groups respondents') . ' / '.Yii::t('app', 'Update');
$this->breadcrumbs=array(
	Yii::app()->getModule('respondent')->t('Groups respondents')=>array('index'),
	Yii::app()->getModule('respondent')->t('Update group'),
);

$this->menu=array(
	array('label'=>Yii::app()->getModule('respondent')->t('List groups'), 'url'=>array('index')),
	array('label'=>Yii::app()->getModule('respondent')->t('Create group'),'url'=>array('create')),
);
?>

<?php
	$action = Yii::t('app','Update').' '.Yii::app()->getModule('respondent')->t('Groups respondents');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'clients'=>$clients, 'action'=>$action)); ?>