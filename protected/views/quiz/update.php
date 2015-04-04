<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app', 'Update '.$model->type);

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array(array('/'.$model->type.'/'.$model->quiz_id.'/update'), "active"),
    Yii::t('app', 'Wizard Questions') => array('/'.$model->type.'/'.$model->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Audience') => array('/'.$model->type.'/'.$model->quiz_id.'/targetAudience'),
	Yii::t('app', 'Wizard Collection') => array('/'.$model->type.'/'.$model->quiz_id.'/collection'),
	Yii::t('app', 'Wizard Launch') => array('/'.$model->type.'/'.$model->quiz_id.'/launch'),
);

if ($model->state != Quiz::STATE_EDIT) {
	$this->menu=array(
		array('label'=>Yii::t('app', 'Results'),'url'=>array($model->type."/".$model->quiz_id."/Applications")),
		array('label'=>Yii::t('app', 'Statistics'),'url'=>array($model->type."/".$model->quiz_id."/statistics")),
		array('label'=>Yii::t('app', 'Comments'),'url'=>array($model->type."/".$model->quiz_id."/comments")),
		array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($model->type."/".$model->quiz_id."/export")),
	);
}
?>
<?php
	$action = Yii::t('app', 'Update '.$model->type);;
?>

<?php echo $this->renderPartial('_form',array(
    'model'=>$model,
    'user'=>$user,
    'typeState'=> $nameTypeState,
    'action'=> $action
)); ?>