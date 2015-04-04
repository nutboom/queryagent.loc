<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' / '.Yii::t('app','Applications') . ' / '.Yii::t('app','View application');
$this->breadcrumbs=array(
        Yii::t('app', ucfirst($quiz->type).'s')=>array('/'.$quiz->type),
        Yii::t('app', 'Applications')=>array("$quiz->type/$quiz->quiz_id/Applications"),
	Yii::t('app', 'View application'),
);

if ($quiz->state != Quiz::STATE_EDIT) {
	$this->menu=array(
		array('label'=>Yii::t('app', 'Results'),'url'=>array($quiz->type."/".$quiz->quiz_id."/Applications")),
		array('label'=>Yii::t('app', 'Statistics'),'url'=>array($quiz->type."/".$quiz->quiz_id."/statistics")),
		array('label'=>Yii::t('app', 'Comments'),'url'=>array($quiz->type."/".$quiz->quiz_id."/comments")),
		array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($quiz->type."/".$quiz->quiz_id."/export")),
	);
}
?>

<h2><?php echo Yii::t('app', 'View application'); ?></h2>

<dl class="dl-horizontal">
    <dt><?php echo Yii::t('app', ucfirst($quiz->type)); ?></dt>
    <dd>&#171;<?php echo $quiz->title; ?>&#187;</dd>

    <dt><?php echo Yii::app()->getModule('respondent')->t('Respondent'); ?></dt>
    <dd>
        <?php echo $respondent->fullName; ?><br/>
        <?php echo SubStr($respondent->phone_number, 0, -4)."XXXX"; ?>
    </dd>
</dl>

<?php $this->widget('application.widgets.QAQuestionaryWidget', array('application'=>$model)); ?>

<?php $this->renderPartial('comment/_view',array(
    'quiz'=>$quiz,
    'model'=>$model,
    'comments'=>$model->comments,
    'comment'=>$comment,
  )); ?>