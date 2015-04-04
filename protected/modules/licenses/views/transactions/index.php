<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Transactions');
$this->breadcrumbs=array(
    Yii::t('app','Transactions'),
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
        array('name'=>'id', 'header'=>Yii::t('app','ID')),
        array('name'=>'user', 'header'=>Yii::t('app','User'), 'value'=>'$data->manager->username'),
        array('name'=>'summ', 'header'=>Yii::t('app','Summ')),
        array('name'=>'status', 'header'=>Yii::t('app','Status'), 'value'=>'Transactions::itemAlias($data->status)'),
        array('name'=>'date_open', 'header'=>Yii::t('app','Date Open')),
        array('name'=>'date_close', 'header'=>Yii::t('app','Date Close')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>