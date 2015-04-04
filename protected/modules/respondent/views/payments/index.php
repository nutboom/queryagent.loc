<?php
$this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents').' '.RespondentModule::t('Respondent Payments');
$this->breadcrumbs=array(
        RespondentModule::t('Respondents')=>array('/respondent'),
	RespondentModule::t('Respondent Payments'),
);

$this->menu=array(
        array('label'=>RespondentModule::t('List Respondents'),'url'=>array('/respondent')),
);
?>

<h1><?php echo $respondent->last_name.'&nbsp;'.$respondent->first_name; ?></h1>
<h3><?php echo RespondentModule::t('Respondent Payments'); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array(
            'name'=>'datetime',
            'value'=>'Utils::unpack_datetime($data->datetime)',
        ),
        'money',
        array(
                'name'=>'state',
                'value'=>'Payments::itemAlias("PaymentsState",$data->state)',
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'type',
                'value'=>'Payments::itemAlias("PaymentsType",$data->type)',
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        'comment',
    ),
)); ?>
