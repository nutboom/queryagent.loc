<?php
    $this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' - '.Yii::t('app','Applications');
    $this->breadcrumbs=array(
            Yii::t('app', ucfirst($quiz->type).'s')=>array('/'.$quiz->type),
            Yii::t('app', 'Applications'),
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

<h2><?php echo Yii::t('app', ucfirst($quiz->type)); ?>&nbsp;&#171;<?php echo $quiz->title; ?>&#187;</h2>
<h3><?php echo Yii::t('app', 'Applications'); ?></h3>

<?php $this->renderPartial('_list',array(
    'quiz'=>$quiz,
    'model'=>$model,
  )); ?>
