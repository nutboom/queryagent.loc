<?php

class PaymentsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
			'accessControl', // perform access control for CRUD operations
		));
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index'),
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
            if($_GET['id']){
                $dataProvider=new CActiveDataProvider('Payments', array(
                    'criteria'=>array(
                        'condition'=>'respondent_id = '.$_GET['id'],
                    ),

                    'pagination'=>array(
                        'pageSize'=>Yii::app()->controller->module->respondent_page_size,
                    ),
		));
            } else
                $dataProvider=new CActiveDataProvider('Payments');

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'respondent'=>  $this->loadModelRespondent($_GET['id']),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelRespondent($id)
	{
		$model = Respondent::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, 'Respondent not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='respondent-payments-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
