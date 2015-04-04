<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Check questions and answers');
$this->breadcrumbs=array(
    Yii::t('app','Templates') => array('/catalog'),
	Yii::t('app','Check questions and answers'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Create dict'),'url'=>array('create')),
);
?>

<h1><?php echo '&#171;'.Yii::t('app','Check questions and answers').'&#187;'; ?></h1>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'text', 'header'=>Yii::t('app','Text question')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
