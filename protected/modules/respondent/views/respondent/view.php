<?php
$this->layout='//layouts/column2';
$this->pageTitle=Yii::app()->name . ' / '. RespondentModule::t('Respondents').' / '.$model->last_name.' '.$model->first_name;
$this->breadcrumbs=array(
	RespondentModule::t('Respondents')=>array('index'),
	$model->last_name.' '.$model->first_name,
);

$this->menu=array(
        array('label'=>RespondentModule::t('List Respondents'),'url'=>array('/respondent')),
);
?>

<h1><?php echo $model->last_name.'&nbsp;'.$model->first_name; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'type'=>'striped bordered condensed',
    'data'=>$model,
    'attributes'=>array(
        array(
                'name'=>'sex',
                'value'=>Respondent::itemAlias("RespondentGender",$model->sex),
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'birth_date',
                'value'=>  Utils::unpack_date($model->birth_date),
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'marital_state',
                'value'=>Respondent::itemAlias("RespondentMaritalState",$model->marital_state),
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
    ),
)); ?>

<?php if($model->phone_number): ?>
    <?php $this->widget('bootstrap.widgets.TbDetailView', array(
        'type'=>'striped bordered condensed',
        'data'=>$model,
        'attributes'=>array(
            'phone_number',
            array(
                    'name'=>'phone_is_confirmed',
                    'value'=>Respondent::itemAlias("PhoneConfirmedStatus",$model->phone_is_confirmed),
                    //'filter'=>User::itemAlias("AdminStatus"),
            ),
        ),
    )); ?>
<?php endif; ?>

<?php if($model->email_actual): ?>
    <?php $this->widget('bootstrap.widgets.TbDetailView', array(
        'type'=>'striped bordered condensed',
        'data'=>$model,
        'attributes'=>array(
            'email_actual',
        ),
    )); ?>
<?php endif; ?>

<?php if($model->social_type): ?>
    <?php $this->widget('bootstrap.widgets.TbDetailView', array(
        'type'=>'striped bordered condensed',
        'data'=>$model,
        'attributes'=>array(
            array(
                    'name'=>'social_type',
                    'value'=>Respondent::itemAlias("RespondentSocial",$model->social_type),
                    //'filter'=>User::itemAlias("AdminStatus"),
            ),
        ),
    )); ?>
<?php endif; ?>

<?php
	$position	= ($model->position) ? $model->position->title : RespondentModule::t('None');
	$scope		= ($model->scope) ? $model->scope->title : RespondentModule::t('None');
	$city		= ($model->city) ? $model->city->title : RespondentModule::t('None');
	$country	= ($model->country) ? $model->country->title : RespondentModule::t('None');
?>
<?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'type'=>'striped bordered condensed',
    'data'=>$model,
    'attributes'=>array(
        array(
                'name'=>'position_id',
                'value'=>$position,
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'scope_id',
                'value'=>$scope,
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'city_id',
                'value'=>$city,
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        array(
                'name'=>'country_id',
                'value'=>$country,
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
    ),
)); ?>


<?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'type'=>'striped bordered condensed',
    'data'=>$model,
    'attributes'=>array(
        array(
                'name'=>'state_id',
                'value'=>$model->state->title,
                //'filter'=>User::itemAlias("AdminStatus"),
        ),
        'income',
        'money',
        'karma',
    ),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>new CArrayDataProvider($model->educations, array('keyField'=>'dict_education_id')),
    'template'=>"{items}",
    'columns'=>array(
        array(
            'header'=>Yii::t('app','Educations'),
            'name' => 'title',
            'htmlOptions' => array('class' => 'span4'),
        ),
    )
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>new CArrayDataProvider($model->getClassificQuestions(), array('keyField'=>'question')),
    'template'=>"{items}",
    'columns'=>array(
        array(
                'header'=>Yii::t('app','Classifying question'),
                'name' => 'question',
                'htmlOptions' => array('class' => 'span4'),
        ),
        array(
                'header'=>Yii::t('app','Answer'),
                'name' => 'answer',
        ),
    )
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>new CArrayDataProvider($model->groups, array('keyField'=>'id')),
    'template'=>"{items}",
    'columns'=>array(
        array(
            'header'=>RespondentModule::t('Groups respondents'),
            'name' => 'title',
            'htmlOptions' => array('class' => 'span4'),
        ),
    )
)); ?>