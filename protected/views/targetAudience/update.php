<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' / '.Yii::t('app','Target audience') . ' / '.Yii::t('app','Update target audience');

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
    Yii::t('app', 'Wizard Questions') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Audience') => array(array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience'), "active"),
	Yii::t('app', 'Wizard Collection') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/collection'),
	Yii::t('app', 'Wizard Launch') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/launch'),
);
?>

<?php echo $this->renderPartial('_form',array('model'=>$model, 'client'=>$quiz->client_id, 'quiz'=>$quiz, 'formtype'=>'update')); ?>