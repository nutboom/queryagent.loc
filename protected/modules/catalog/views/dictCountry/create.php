<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Countries') . ' / '.Yii::t('app','Create dict');
$this->breadcrumbs=array(
	Yii::t('app','Dict').' '.Yii::t('app','Countries and cities')=>array('/catalog/dictCity'),
	Yii::t('app','Create dict'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List dict'),'url'=>array('/catalog/dictCity')),
);
?>

<?php
	$action = Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Countries').'&#187;';
?>
<?php echo $this->renderPartial('_form', array('model'=>$model,'action'=>$action)); ?>