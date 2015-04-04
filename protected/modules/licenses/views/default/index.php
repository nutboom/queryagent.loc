<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Tariffs');
$this->breadcrumbs=array(
    Yii::t('app', 'Licences'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Tariffs'), 'url'=>array('/licenses/tariffs')),
	array('label'=>Yii::t('app','Transactions'), 'url'=>array('/licenses/transactions')),
);
?>