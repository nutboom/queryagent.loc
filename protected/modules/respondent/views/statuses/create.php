<?php
$this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents').' '.RespondentModule::t('Respondent Statuses') . ' / '. Yii::t('app','Create dict');
$this->breadcrumbs=array(
	RespondentModule::t('Respondents')=>array('/respondent'),
	RespondentModule::t('Respondent Statuses')=>array('index'),
	UserModule::t('Create'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List dict'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app', 'Dict').'&nbsp;&#171;'.RespondentModule::t('Respondent Statuses').'&#187;';
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>