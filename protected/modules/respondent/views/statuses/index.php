<?php
$this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents').' '.RespondentModule::t('Respondent Statuses');
$this->breadcrumbs=array(
        RespondentModule::t('Respondents')=>array('/respondent'),
	RespondentModule::t('Respondent Statuses'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Create'),'url'=>array('create')),
	array('label'=>RespondentModule::t('List Respondents'),'url'=>array('/respondent')),
);
?>

<h1><?php echo Yii::t('app', 'Dict').'&nbsp;&#171;'.RespondentModule::t('Respondent Statuses').'&#187;'; ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        'karma',
        'multiplicator',
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
