<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' / '.Yii::t('app','Target audience');

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
	Yii::t('app', 'Wizard Questions') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Audience') => array(array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience'), "active"),
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

<h2><?php echo Yii::t('app', ucfirst($quiz->type)); ?>&nbsp;&#171;<?php echo $quiz->title; ?>&#187;</h2>
<h3><?php echo Yii::t('app', 'Target audience'); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array(
            'header'=>Yii::t('app','Age'),
            'value'=>'$data->concatAge()',
        ),
        array(
            'header'=>Yii::t('app','Income'),
            'value'=>'$data->concatIncome()',
        ),
        array(
            'header'=>Yii::t('app','Gender'),
            'value'=>'TargetAudience::itemAlias("GenderAudience",$data->gender)',
        ),
        array(
            'header'=>Yii::t('app','Marital State'),
            'value'=>'TargetAudience::itemAlias("MaritalStateAudience",$data->marital_state)',
        ),
        /*array(
            'header'=>Yii::t('app','Minimal User State'),
            'value'=>'$data->minimalUserState->title',
        ),*/
        array(
            'header'=>Yii::t('app','Count Limit'),
            'value'=>'$data->count_limit',
        ),
        array(
            'header'=>Yii::t('app','Number of respondents'),
            'value'=>array($this,'gridCountRespondentsRow'),
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'updateButtonUrl'=>'Yii::app()->createUrl("'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/update/".$data->primaryKey)',
            'deleteButtonUrl'=>'Yii::app()->createUrl("'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/delete/".$data->primaryKey)',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>


<?php
    if ($addButton) {
		$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'link',
			'type'=>'primary',
			'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/create'),
			'label'=>Yii::t('app','Create target audience'),
		));
	}
?>

<div align="right" class="lead muted">
    <em><?php echo Yii::t('app','Total respondents'); ?>:&nbsp;<?php echo $this->gridCountRespondentsAll($quiz->quiz_id); ?></em>
    <br />
    <em><?php echo Yii::t('app','Total cost of '.$quiz->type); ?>:&nbsp;<?php echo $this->gridCountRespondentsAll($quiz->quiz_id)*$quiz->money; ?> <?php echo Yii::t('app','rub dot'); ?></em>
</div>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'link',
	'type'=>'primary',
	'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
	'label'=>Yii::t('app','Back'),
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'link',
	'type'=>'primary',
	'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'),
	'label'=>Yii::t('app','Next'),
)); ?>