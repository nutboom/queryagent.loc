<?php
$this->pageTitle=Yii::app()->name . ' / '. UserModule::t('Users');
$this->breadcrumbs=array(
	UserModule::t("Users"),
);
if(UserModule::isAdmin()) {
	$this->layout='//layouts/column2';
	$this->menu=array(
            array('label'=>UserModule::t('Create User'), 'url'=>array('admin/create')),
        );
}
?>

<h1><?php echo UserModule::t("List User"); ?></h1>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'user-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'template'=>"{items}\n{pager}",
        'pagerCssClass'=>'pagination pagination-right',
        'columns'=>array(
		array(
			'name' => 'username',
			'type'=>'raw',
			'value' => 'CHtml::link(UHtml::markSearch($data,"username"),array("admin/view","id"=>$data->id))',
		),
		array(
			'name'=>'email',
			'type'=>'raw',
			'value'=>'CHtml::link(UHtml::markSearch($data,"email"), "mailto:".$data->email)',
		),
		array(
			'name'=>'Quizs',
			'type'=>'raw',
			'value'=>'CHtml::link(count($data->quizs),array("/quiz", "manager_id"=>$data->id))',
		),
		array(
			'name'=>'Missions',
			'type'=>'raw',
			'value'=>'CHtml::link(count($data->missions),array("/quiz","manager_id"=>$data->id))',
		),
		'create_at',
		'lastvisit_at',
		array(
			'name'=>'superuser',
			'value'=>'User::itemAlias("AdminStatus",$data->superuser)',
			//'filter'=>User::itemAlias("AdminStatus"),
		),
		array(
			'name'=>'manager',
			'value'=>'User::itemAlias("ManagerStatus",$data->manager)',
			//'filter'=>User::itemAlias("ManagerStatus"),
		),
		array(
			'name'=>'status',
			'value'=>'User::itemAlias("UserStatus",$data->status)',
			//'filter' => User::itemAlias("UserStatus"),
		),
                array(
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'template'=>'{clients}',
                    'buttons'=>array
                    (
                        'clients' => array
                        (
                            'label'=>Yii::t('app','Clients'),
                            'url'=>'Yii::app()->controller->createUrl("admin/clients", array("id"=>$data->primaryKey))',
                            'icon'=>'icon-user',
                        ),
                    ),
                ),
		array(
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'viewButtonUrl'=>'Yii::app()->controller->createUrl("admin/view",array("id"=>$data->primaryKey))',
                    'updateButtonUrl'=>'Yii::app()->controller->createUrl("admin/update",array("id"=>$data->primaryKey))',
                    /*'deleteButtonUrl'=>'Yii::app()->controller->createUrl("admin/delete",array("id"=>$data->primaryKey))',*/
                    'htmlOptions'=>array('style'=>'width: 50px'),
                ),
	),
    ));
?>
