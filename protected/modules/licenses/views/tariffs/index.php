<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Tariffs');
$this->breadcrumbs=array(
    Yii::t('app','Tariffs'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<?php
$this->widget('bootstrap.widgets.TbAlert', array(
	'block'=>true, // display a larger alert block?
	'fade'=>true, // use transitions?
	'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
	'alerts'=>array( // configurations per alert type
		'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
	),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'name', 'header'=>Yii::t('app','Title')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>