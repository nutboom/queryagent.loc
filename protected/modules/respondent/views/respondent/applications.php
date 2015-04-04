<?php
    $this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents').' / '.$respondent->last_name.' '.$respondent->first_name . ' / '. RespondentModule::t('Respondent aplications');
    $this->breadcrumbs=array(
            RespondentModule::t('Respondents')=>array('index'),
            RespondentModule::t('Respondent aplications'),
    );
?>

<h1><?php echo $respondent->last_name.'&nbsp;'.$respondent->first_name; ?></h1>
<h3><?php echo RespondentModule::t('Respondent aplications'); ?></h3>

<?php $this->renderInternal(Yii::getPathOfAlias('application.views.applications._list').'.php',array(
    'quiz'=>$quiz,
    'model'=>$model,
  )); ?>