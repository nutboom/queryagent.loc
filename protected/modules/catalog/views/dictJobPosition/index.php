<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Job positions');
$this->breadcrumbs=array(
	Yii::t('app','Dict') => array('/catalog'),
    Yii::t('app','Job positions'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<h1><?php echo Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Job positions').'&#187;'; ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
