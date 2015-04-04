<?php
    $this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app','Comments '.$model->type);
    $this->breadcrumbs=array(
            Yii::t('app', ucfirst($model->type).'s')=>array('/'.$model->type),
            Yii::t('app', 'Comments '.$model->type),
    );

    $this->menu=array(
	array('label'=>Yii::t('app', ucfirst($model->type).'s'),'url'=>array('/'.$model->type)),
    );
?>

<h2><?php echo Yii::t('app', ucfirst($model->type)); ?>&nbsp;&#171;<?php echo $model->title; ?>&#187;</h2>
<h3><?php echo Yii::t('app', 'Comments '.$model->type); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'id'=>'application-comment-grid',
    'dataProvider'=>new CArrayDataProvider($model->comments),
    'template'=>"{items}",
    'columns'=>array(
        array(
            'header'=>QuizComment::model()->getAttributeLabel('respondent_id'),
            'name'=>'respondent_id',
            'value'=>'$data->respondent->last_name." ".$data->respondent->first_name." ".$data->respondent->phone_number',
        ),
        array(
            'header'=>QuizComment::model()->getAttributeLabel('text'),
            'name'=>'text',
        ),
        array(
            'header'=>QuizComment::model()->getAttributeLabel('date_created'),
            'name'=>'date_created',
            'value'=>'Utils::unpack_datetime($data->date_created)',
            'htmlOptions'=>array('width'=>'115')
        ),
    ),
)); ?>
