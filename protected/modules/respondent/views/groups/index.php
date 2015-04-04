<?php
/* @var $this GroupRespondentsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	Yii::app()->getModule('respondent')->t('Groups respondents'),
);

$this->menu=array(
	array('label'=>Yii::app()->getModule('respondent')->t('Create group'), 'url'=>array('create')),
);
?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
)); ?>

<h1><?php echo Yii::app()->getModule('respondent')->t('Groups respondents'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array('name'=>'client_id', 'header'=>Yii::t('app','Client Name'), 'value'=>'$data->client->name'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{participants}',
            'buttons'=>array
            (
                'participants' => array
                (
                    'label'=>RespondentModule::t('Participants'),
                    'url'=>'Yii::app()->controller->createUrl("view", array("id"=>$data->primaryKey))',
                    'icon'=>'icon-user',
                ),
            ),
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
