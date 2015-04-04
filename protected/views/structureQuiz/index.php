<!-- Init jqDynaForm -->
<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jquery-ui-1.8.20.custom.min.js'); ?>
<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/ui-lightness/jquery-ui-1.8.20.custom.css'); ?>

<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/jqDynaForm.css'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jqDynaForm.js'); ?>

<?php Yii::app()->getClientScript()->registerScriptFile('/js/ajaxfileupload/ajaxfileupload.js'); ?>

<?php
/* @var $this StructureQuizController */

$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' / '.Yii::t('app','Content '.$quiz->type);

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
    Yii::t('app', 'Wizard Questions') => array(array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'), "active"),
	Yii::t('app', 'Wizard Audience') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience'),
	Yii::t('app', 'Wizard Collection') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/collection'),
	Yii::t('app', 'Wizard Launch') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/launch'),
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

<div id="structureQuiz">
    <?php echo $this->renderPartial('_form', array(
        'model'=>$model,
        'groups'=>$groups,
        'question'=>$question,
        'quiz'=>$quiz,
    )); ?>
</div>

<script>
    $(document).on('ready',function(){
        $("a[data-target='#conditionsModal_1']").remove()
    })
</script>
