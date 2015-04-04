<?php
$this->pageTitle=Yii::app()->name . ' / '. UserModule::t('Users') . ' / '.UserModule::t('Create');
$this->breadcrumbs=array(
	UserModule::t('Users')=>array('/user'),
	UserModule::t('Create'),
);

$this->menu=array(
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
);
?>

<?php
	$action = UserModule::t("Create User");;
?>

<?php
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile,'action'=>$action));
?>