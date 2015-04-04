<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Job positions') . ' / '.Yii::t('app','Create dict');
$this->breadcrumbs=array(
	Yii::t('app','Dict').' '.Yii::t('app','Job positions')=>array('index'),
	Yii::t('app','Create dict'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List dict'),'url'=>array('index')),
);
?>

<?php
	$action = Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Job positions').'&#187;';
?>
<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>