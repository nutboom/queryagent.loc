<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app','Tariffs') . ' / '.Yii::t('app','Update tariff');
$this->breadcrumbs=array(
	Yii::t('app','Tariffs')=>array('index'),
	Yii::t('app','Update tariff'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List tariffs'),'url'=>array('index')),
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>
<?php
	$action = Yii::t('app','Update tariff');
?>

<?php echo $this->renderPartial('_form',array('model'=>$model,'action'=>$action)); ?>