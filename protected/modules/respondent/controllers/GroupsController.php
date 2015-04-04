<?php

class GroupsController extends Controller
{
        public $layout='//layouts/column2';
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
                    'actions'=>array('index','view', 'create', 'update', 'remove'),
                    'users'=>array_merge(Yii::app()->getModule('user')->getManagerMarketers(), Yii::app()->getModule('user')->getClientUsers(), Yii::app()->getModule('user')->getAdmins()),
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
                'respondents' => new CArrayDataProvider($model->respondents)
            ));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
            $criteria = array();
            if(!Yii::app()->getModule('user')->isAdmin())
                $criteria=new CDbCriteria(array(
                        'condition'=>'manager_id = '.Yii::app()->user->id,

                ));
            $dataProvider=new CActiveDataProvider('GroupRespondents', array(
                'criteria'=>$criteria,
            ));
            $this->render('index',array(
                    'dataProvider'=>$dataProvider,
            ));
	}

        /**
	 * Create a new item models
	 */
	public function actionCreate() {
            $model = new GroupRespondents;
            $quiz = Quiz::model()->findByPk($_GET['quiz']);

            if(isset($_POST['GroupRespondents'])) {
				$model->attributes=$_POST['GroupRespondents'];
				$model->manager = array(User::model()->findByPk(Yii::app()->user->id));
				$model->textarea = $_POST['GroupRespondents']['textarea'];

				if(isset($_POST['GroupRespondents']['respondents'])){
					$model->deleteConnectionWithRespondents($_POST['GroupRespondents']['respondents']);
					$criteria = new CDbCriteria();
					$criteria->addInCondition('id', $_POST['GroupRespondents']['respondents']);
					$respondents = Respondent::model()->findAll($criteria);

					$model->respondents = $respondents;
				}

				if($model->withRelated->save(true, array('respondents'))){
					//$this->redirect(array('index'));
					Yii::app()->user->setFlash('success', RespondentModule::t('Group of respondents successfully saved'));
					if (isset($_GET['quiz'])) {
						$this->redirect(array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/create'));
					}

					$this->refresh();
				}
            }

            $arrayClients = Yii::app()->user->getClients();
            if(count($arrayClients) > 0) {
				$model->client_id = current($arrayClients)->id;
			}
            else {
				$model->client_id = NULL;
			}

            $this->render('create',array(
				'model'=>$model,
				'quiz'=>$quiz,
				'clients'=>$arrayClients,
            ));
	}

        /**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['GroupRespondents'])) {
			$model->attributes=$_POST['GroupRespondents'];
			$model->textarea = $_POST['GroupRespondents']['textarea'];

			if(isset($_POST['GroupRespondents']['respondents'])){
				#$model->deleteConnectionWithRespondents($_POST['GroupRespondents']['respondents']);
				$criteria = new CDbCriteria();
				$criteria->addInCondition('id', $_POST['GroupRespondents']['respondents']);
				$respondents = Respondent::model()->findAll($criteria);

				$model->respondents = $respondents;
			}

			if($model->withRelated->save(true, array('respondents'))) {
				Yii::app()->user->setFlash('success', RespondentModule::t('Group of respondents successfully updated'));
				$this->redirect(array('index'));
                                //$this->refresh();
 			}
		}

		$this->render('update',array(
			'model'=>$model,
			'clients'=>Yii::app()->user->getClients(),
		));
	}

        /**
	 * Deletes a respondent from particular model.
	 * @param integer $id the ID of the respondent to be deleted
	 */
	public function actionRemove($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			Respondent::model()->findbyPk($id)->deleteConnectionWithGroup();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('view', array('id'=>$$_GET['id'])));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

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
				$this->_model=GroupRespondents::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

}