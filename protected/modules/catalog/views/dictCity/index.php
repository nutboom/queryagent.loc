<?php
$this->pageTitle=Yii::app()->name . ' / '. Yii::t('app','Dict').' '.Yii::t('app','Countries and cities');
$this->breadcrumbs=array(
    Yii::t('app','Dict') => array('/catalog'),
	Yii::t('app','Countries and cities'),
);

/*$this->menu=array(
	array('label'=>Yii::t('app','Countries'),'url'=>Yii::app()->getModule('catalog')->dictCountryUrl),
	array('label'=>Yii::t('app','Create'),'url'=>array('create')),
);*/
?>

<h2><?php echo Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Cities').'&#187;'; ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProviderCities,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array('name'=>'country_id', 'header'=>Yii::t('app','Country ID'), 'value'=>'$data->country->title'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'header'=>'<a class="create" href="/catalog/dictCity/create" rel="tooltip" data-original-title="'.UserModule::t("Create").' '.Yii::t("app","Cities").'"><i class="icon-plus"></i></a>',
        ),
    ),
)); ?>

<h2><?php echo Yii::t('app','Dict').'&nbsp;&#171;'.Yii::t('app','Countries').'&#187;'; ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProviderCountries,
    'template'=>"{items}\n{pager}",
    'pagerCssClass'=>'pagination pagination-right',
    'columns'=>array(
        array('name'=>'title', 'header'=>Yii::t('app','Title')),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'header'=>'<a class="create" href="/catalog/dictCountry/create" rel="tooltip" data-original-title="'.UserModule::t("Create").' '.Yii::t("app","Countries").'"><i class="icon-plus"></i></a>',
            'updateButtonUrl'=>'Yii::app()->createUrl("catalog/dictCountry/update/id/".$data->primaryKey)',
            'deleteButtonUrl'=>'Yii::app()->createUrl("catalog/dictCountry/delete/id/".$data->primaryKey)',
        ),
    ),
)); ?>

