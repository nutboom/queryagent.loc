<!-- Init jqDynaForm -->
<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jquery-ui-1.8.20.custom.min.js'); ?>
<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/ui-lightness/jquery-ui-1.8.20.custom.css'); ?>

<?php Yii::app()->getClientScript()->registerCssFile('/js/jqDynaForm/jqDynaForm.css'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile('/js/jqDynaForm/jqDynaForm.js'); ?>

<?php Yii::app()->getClientScript()->registerScriptFile('/js/ajaxfileupload/ajaxfileupload.js'); ?>

<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', 'Quizs templates');
$this->breadcrumbs=array(
	Yii::t('app','Dict') => array('/catalog'),
    Yii::t('app','Quizs templates'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<div id="structureQuiz">
    <?php echo $this->renderPartial('_form', array(
        'model'=>$model,
        'groups'=>$groups,
        'question'=>$question,
        'quiz'=>$quiz,
    )); ?>
</div>
