<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Scopes of activity') . ' / '.Yii::t('app','Update dict');
$this->breadcrumbs=array(
	Yii::t('app','Dict').' '.Yii::t('app','Scopes of activity')=>array('index'),
	Yii::t('app','Update dict'),
);

$this->menu=array(
	array('label'=>Yii::t('app','List dict'),'url'=>array('index')),
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<?php
	$action = Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Scopes of activity').'&#187;';
?>
<?php echo $this->renderPartial('_form',array('model'=>$model,'action'=>$action)); ?>