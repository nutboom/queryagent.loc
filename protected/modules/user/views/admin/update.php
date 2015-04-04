<?php
$this->pageTitle=Yii::app()->name . ' / '. UserModule::t('Users') . ' / '.UserModule::t('Update User');
$this->breadcrumbs=array(
	(UserModule::t('Users'))=>array('/user'),
	$model->username=>array('view','id'=>$model->id),
	(UserModule::t('Update User')),
);
$this->menu=array(
    array('label'=>UserModule::t('Create User'), 'url'=>array('create')),
    array('label'=>UserModule::t('View User'), 'url'=>array('view','id'=>$model->id)),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
);
?>

<?php
	$action = UserModule::t('Update User')." ".$model->username;
?>

<?php
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile,'action'=>$action));
?>