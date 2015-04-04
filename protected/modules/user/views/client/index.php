<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app', 'Clients');
$this->breadcrumbs=array(
	Yii::t('app', 'Clients'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create Client'),'url'=>array('create')),
);
?>

<h1><?php echo Yii::t('app','List Client'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
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
        array('name'=>'name', 'header'=>Yii::t('app','Name')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} <span style="float: right;">{delete}</span>',
            'htmlOptions'=>array('style'=>'width: 70px'),
        ),
    ),
)); ?>
