<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Finanse");

$breadcrumbs=array(
	UserModule::t("Finanse")=>array('/user/finance'),
	UserModule::t("Sub Users"),
);
$this->breadcrumbs=$breadcrumbs;

$this->menu=array(
    array('label'=>UserModule::t('Create Sub User'), 'url'=>array('createsub')),
);
?>
<h1><?php echo UserModule::t('Sub Users')?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'username', 'header'=>Yii::t('app','username')),
        array('name'=>'email', 'header'=>Yii::t('app','E-mail')),
        array('name'=>'lastvisit_at', 'header'=>Yii::t('app','Last Visit At')),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'<span class="btn-group">{delete}</span>',
            'buttons'=>array(
                'delete' => array(
                    'options'=>array('class'=>'btn'),
                    'url'=>'Yii::app()->createUrl("user/finance/deletesub",array("id"=>$data->primaryKey))',
                ),
            ),
			'htmlOptions'=>array('style'=>'width: 50px'),
		),
    ),
)); ?>


