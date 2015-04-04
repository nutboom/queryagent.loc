<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Scopes of activity');
$this->breadcrumbs=array(
	Yii::t('app','Dict') => array('/catalog'),
    Yii::t('app','Scopes of activity'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<h1><?php echo Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Scopes of activity').'&#187;'; ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array('name'=>'is_job', 'header'=>Yii::t('app','Is Job'), 'value'=>'DictScope::itemAlias("IsJob",$data->is_job)',),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
