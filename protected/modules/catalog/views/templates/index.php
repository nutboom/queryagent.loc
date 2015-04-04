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

<h1><?php echo Yii::t('app','Quizs templates'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array('name'=>'type', 'header'=>Yii::t('app','Type'), 'value'=>'($data->type == "quiz") ? Yii::t("app", "Quiz") : Yii::t("app", "Mission")',),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
