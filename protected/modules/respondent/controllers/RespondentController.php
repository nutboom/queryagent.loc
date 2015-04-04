<?php

class RespondentController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */

        /**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

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
				'actions'=>array('index','view','applications','sendSms','denyAccess','blocked'),
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
                $model=new Respondent('search');
                $model->unsetAttributes();  // clear any default values
                if(isset($_GET['Respondent']))
                        $model->attributes=$_GET['Respondent'];

                $this->render('index',array(
                        'model'=>$model,
                ));
	}

        /**
	 * Displays Lists a models blocked respondent.
	 */
	public function actionBlocked()
	{
                $model=new Respondent('search');
                $model->unsetAttributes();  // clear any default values
                if(isset($_GET['Respondent']))
                        $model->attributes=$_GET['Respondent'];
                $model->blocked = 1;

                $this->render('index',array(
                        'model'=>$model,
                ));
	}

        /**
	 * Displays Lists a particular model.
	 */
	public function actionApplications($id)
	{
                $respondent = $this->loadModel();
                $model=new Application('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Application']))
			$model->attributes=$_GET['Application'];
                $model->respondent_id = $id;

		$this->render('applications',array(
			'model'=>$model,
			'quiz'=>null,
			'respondent'=>$respondent,

		));
	}

        /**
	 * SET a particular model is BLOCKED.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDenyAccess($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel($id);
                        $model->blocked = 1;
                        $model->save();
                        $model->sessions->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionSendSms($id) {
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$model = $this->loadModel($id);
			echo $model->phone_number;
			Utils::send_sms($model->phone_number, Yii::t('app', 'Confirmation code phone').': '.$model->phone_code);


			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=Respondent::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadRespondent($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=Respondent::model()->findbyPk($id!==null ? $id : $_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='respondent-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
