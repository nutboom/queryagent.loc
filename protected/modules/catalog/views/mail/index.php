<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app','Mail templates');
$this->breadcrumbs=array(
	Yii::t('app','Dict') => array('/catalog'),
    Yii::t('app','Mail templates'),
);

$this->menu=array(
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);
?>

<h1><?php echo Yii::t('app','Mail templates'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'name', 'header'=>Yii::t('app','Name of email template')),
        array('name'=>'title', 'header'=>Yii::t('app','Заголовок письма')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
