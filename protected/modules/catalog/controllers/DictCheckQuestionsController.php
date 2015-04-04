<?php

class DictCheckQuestionsController extends Controller
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
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('deny',  // deny all users
                'actions'=>array('view','admin'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','create','update','delete','deleteAnswer'),
				'users'=>Yii::app()->getModule('user')->getManagers(),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {
		$model = new DictCheckQuestions;
		$modelAnswer = new DictCheckAnswers;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DictCheckQuestions'])) {
			$model->attributes=$_POST['DictCheckQuestions'];

			$manager = (Yii::app()->getModule('user')->isAdmin()) ? "0" : Yii::app()->user->id;
			$model->manager_id = $manager;

			$model->answers = $this->getAnswers($model);

			if ($model->withRelated->save(true, array('answers'))) {
				Yii::app()->user->setFlash('success', Yii::t('app', 'Check question successfully saved'));
				$this->refresh();
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'modelAnswer'=>$modelAnswer,
			'jsonModelAnswer'=>DictCheckAnswers::getJsonStructureAnswers($model->answers),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {

		$model=$this->loadModel($id);
		$modelAnswer = new DictCheckAnswers;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DictCheckQuestions'])) {
			$model->attributes=$_POST['DictCheckQuestions'];

			$model->answers = $this->getAnswers($model);

			if ($model->withRelated->save(true,array('answers'))) {
				Yii::app()->user->setFlash('success', Yii::t('app', 'Check question successfully updated'));
				$this->refresh();
			}
		}

		$this->render('update',array(
			'model'=>$model,
                        'modelAnswer'=>$modelAnswer,
                        'jsonModelAnswer'=>DictCheckAnswers::getJsonStructureAnswers($model->answers),
		));
	}

        /**
	 * Get array array model DictCheckAnswers a particular model.
	 * @param DictCheckQuestions $question the model
	 */
	protected function getAnswers($question) {
            $arrCheckAnswers = array();
            if(isset($_POST['DictCheckAnswers'])) {
                foreach ($_POST['DictCheckAnswers']['id'] as $a => $answer) {
                    if($answer)
                        $checkAnswer = DictCheckAnswers::model()->findByPk($answer);
                    else
                        $checkAnswer = new DictCheckAnswers;

                    if(isset($_POST['DictCheckAnswers']['text'][$a]))
                            $checkAnswer->text = $_POST['DictCheckAnswers']['text'][$a];
                    if(isset($_POST['DictCheckAnswers']['is_true'][$a + 1]))
                            $checkAnswer->is_true = $_POST['DictCheckAnswers']['is_true'][$a + 1];

                    array_push($arrCheckAnswers, $checkAnswer);
                }
            }

            return $arrCheckAnswers;
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
	 * Deletes a answer particular model.
	 */
	public function actionDeleteAnswer() {
		$id = $_POST['id'];
		#echo $id;
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			DictCheckAnswers::model()->findByPk($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                                $this->refresh();
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex() {
		$criteria=new CDbCriteria;
		if (!Yii::app()->getModule('user')->isAdmin()) {
			$criteria->compare('manager_id', Yii::app()->user->id);
		}
		else {
			$criteria->compare('manager_id', '0');
		}

		$dataProvider=new CActiveDataProvider('DictCheckQuestions', array('criteria'=>$criteria,));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DictCheckQuestions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DictCheckQuestions']))
			$model->attributes=$_GET['DictCheckQuestions'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=DictCheckQuestions::model()->with('answers')->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dict-check-questions-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
