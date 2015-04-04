<?php

class ApplicationsController extends Controller
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
                                'actions'=>array('admin','create','update','delete'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
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
	public function actionView($quiz, $id)
	{
                $quizM = $this->loadModelQuiz($quiz);

                $model = $this->loadModel($id);
                $respondent = Yii::app()->getModule('respondent')->respondent($model->respondent_id);
                $comment=$this->newComment($model);

		$this->render('view',array(
			'model'=>$model,
			'quiz'=>$quizM,
			'respondent'=>$respondent,
			'comment'=>$comment,
		));
	}

        /**
	 * Create a particular model ApplicationComment.
	 * @param Application $application the model object
	 */
        protected function newComment($application)
        {
            $comment = new ApplicationComment;

            if(isset($_POST['ApplicationComment'])){
                $comment->attributes=$_POST['ApplicationComment'];

                if($comment->state == Application::STATE_REJECT && !$comment->text){
                    $comment->addError ('text', Yii::t('app', 'State reject: text is empty.'));

                    if(isset($_POST['ajax']) && $_POST['ajax']==='application-comment-Form'){
                        echo CActiveForm::validate($comment);
                        Yii::app()->end();
                    }
                } else{
                    if($comment->state == Application::STATE_CLOSE && !$comment->text){
                        $application->state = $comment->state;
                        $application->save();
                        $this->refresh();
                    } elseif($application->addComment($comment))
                        $this->refresh();
                }
            }

            return $comment;
        }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Application;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Application']))
		{
			$model->attributes=$_POST['Application'];
			if($model->save())
				//$this->redirect(array('view','id'=>$model->id));
                                $this->refresh();
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Application']))
		{
			$model->attributes=$_POST['Application'];
			if($model->save())
				//$this->redirect(array('view','id'=>$model->id));
                                $this->refresh();
		}

		$this->render('update',array(
			'model'=>$model,
		));
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
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($quiz)
	{
                $quizM = $this->loadModelQuiz($quiz);

                $model=new Application('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Application']))
			$model->attributes=$_GET['Application'];

		$this->render('index',array(
			'model'=>$model,
			'quiz'=>$quizM,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Application('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Application']))
			$model->attributes=$_GET['Application'];

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
		$model=Application::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

        public function loadModelQuiz($quiz_id)
	{
		$quiz=Quiz::model()->findByPk($quiz_id);
		if($quiz===null)
			throw new CHttpException(404,'The requested page does not exist.');
                return $quiz;
        }

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='application-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	
}
