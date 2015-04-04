<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Tariffs');
$this->breadcrumbs=array(
    Yii::t('app', 'Templates'),
);

if (Yii::app()->getModule('user')->isAdmin()) {
	$this->menu=array(
		array('label'=>Yii::t('app','Mail templates'), 'url'=>array("/catalog/mail")),
		array('label'=>Yii::t('app','Educations'), 'url'=>Yii::app()->getModule('catalog')->dictEducationUrl),
		array('label'=>Yii::t('app','Job positions'), 'url'=>Yii::app()->getModule('catalog')->dictJobPositionUrl),
		array('label'=>Yii::t('app','Scopes of activity'), 'url'=>Yii::app()->getModule('catalog')->dictScopeUrl),
		array('label'=>Yii::t('app','Countries and cities'), 'url'=>Yii::app()->getModule('catalog')->dictCityUrl),
		array('label'=>Yii::t('app','Check questions and answers'), 'url'=>Yii::app()->getModule('catalog')->dictCheckQuestionsUrl),
	);
}
else {
	$this->menu=array(
		array('label'=>Yii::t('app','Check questions and answers'), 'url'=>Yii::app()->getModule('catalog')->dictCheckQuestionsUrl),
		array('label'=>Yii::t('app','Templates of sms and emails'), 'url'=>array("/catalog/smstpl")),
		array('label'=>Yii::t('app','Templates of quizs'), 'url'=>array("/catalog/templates")),
	);
}
?>