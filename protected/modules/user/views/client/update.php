<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app', 'Clients') . ' / '.Yii::t('app', 'Update Client');
if(!Yii::app()->user->isClient())
    $this->breadcrumbs=array(
        Yii::t('app', 'Clients')=>array('index'),
        Yii::t('app', 'Update Client'),
    );

$this->menu=array(
	array('label'=>Yii::t('app','List Client'),'url'=>array('index'), 'visible'=>!Yii::app()->user->isClient()),
	array('label'=>Yii::t('app','Create Client'),'url'=>array('create'), 'visible'=>!Yii::app()->user->isClient()),
);
?>

<?php
	$action = Yii::t('app','Update Client');
?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'action'=>$action)); ?>