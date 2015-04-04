<?php

class TargetAudienceController extends Controller
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
                        array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','create','update','delete'),
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
		$dataProvider=new CActiveDataProvider('TargetAudience', array(
                    'criteria'=>array(
                        'condition'=>'quiz_id='.$id,
                    ),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($quiz)
	{
		$model=new TargetAudience;
        $quizEl = $this->loadModelQuiz($quiz);

        $model->quiz_id = $quiz;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TargetAudience'])) {
			$model->attributes=$_POST['TargetAudience'];
			$model->minimal_user_state_id = "1";
            $model = $this->setLinks($model);

			if($model->withRelated->save(true, array('educations','scopes','job_position','countries','cities','classfAnswers','groupsRespondents'))) {
				$this->redirect(array('quiz/'.$quiz.'/targetAudience'));
				//$this->refresh();
    		}
		}

		$this->render('create',array(
			'model'=>$model,
			'quiz'=>$quizEl,
		));
	}

	/*
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($quiz,$id) {
		$model=$this->loadModel($id);
		$quizEl = $this->loadModelQuiz($quiz);

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['TargetAudience'])) {
			$model->attributes=$_POST['TargetAudience'];

			$model = $this->setLinks($model);

			if($model->withRelated->save(true, array('educations','scopes','job_position','countries','cities','classfAnswers','groupsRespondents')))
				//$this->redirect(array('quiz/'.$quiz.'/targetAudience'));
                                $this->refresh();
		}

		$this->render('update',array(
			'model'=>$model,
			'quiz'=>$quizEl,
		));
	}

        protected function setLinks($model){
            // Educations
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkEducations);
            if(isset($_POST['TargetAudience']['educations'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_education_id', $_POST['TargetAudience']['educations']);
                $educations = DictEducation::model()->findAll($criteria);

                $model->educations = $educations;
            }

            // Scopes
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkScopes);
            if(isset($_POST['TargetAudience']['scopes'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_scope_id', $_POST['TargetAudience']['scopes']);
                $scopes = DictScope::model()->findAll($criteria);

                $model->scopes = $scopes;
            }

            // Job positions
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkPositions);
            if(isset($_POST['TargetAudience']['job_position'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_job_position_id', $_POST['TargetAudience']['job_position']);
                $positions = DictJobPosition::model()->findAll($criteria);

                $model->job_position = $positions;
            }

            // Classification answers
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkClassfAnswers);
            if(isset($_POST['TargetAudience']['classfAnswers'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('id', $_POST['TargetAudience']['classfAnswers']);
                $classfAnswers = Answer::model()->findAll($criteria);

                $model->classfAnswers = $classfAnswers;
            }

            // Countries
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkCountries);
            if(isset($_POST['TargetAudience']['countries'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_country_id', $_POST['TargetAudience']['countries']);
                $countries = DictCountry::model()->findAll($criteria);

                $model->countries = $countries;
            }

            // Cities
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkCities);
            if(isset($_POST['TargetAudience']['cities'])){
                $arrCities = array();
                foreach ($_POST['TargetAudience']['cities'] as $i=>$value) {
                    $arrCities = array_merge($arrCities, $value);
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_city_id', $arrCities);
                $cities = DictCity::model()->findAll($criteria);

                $model->cities = $cities;
            }

            // Groups respondents
            if(!$model->isNewRecord) $model->deleteConnectionWithTargetAudience($model->tableLinkGroupRespondents);
            if(isset($_POST['TargetAudience']['groupsRespondents'])){
                $criteria = new CDbCriteria();
                $criteria->addInCondition('id', $_POST['TargetAudience']['groupsRespondents']);
                $groups = GroupRespondents::model()->findAll($criteria);

                $model->groupsRespondents = $groups;
            }

            return $model;
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
	 * Lists all models.
	 */
	public function actionIndex($quiz) {
		$quizEl = $this->loadModelQuiz($quiz);

		// if quiz use respondents in all base
		$addButton = true;
		$base = TargetAudience::haveBaseAudience($quizEl);

		if ($quizEl->state == Quiz::STATE_WORK && $base) {
       		$addButton = false;
		}

		$dataProvider=new CActiveDataProvider('TargetAudience', array(
                    'criteria'=>array(
                        'condition'=>'quiz_id='.$quiz,
                    ),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
                        'quiz'=>$quizEl,
                        'addButton'=>$addButton,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new TargetAudience('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TargetAudience']))
			$model->attributes=$_GET['TargetAudience'];

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
		$model=TargetAudience::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='target-audience-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

        // called on rendering a grid row
        // Column count respondents
        protected function gridCountRespondentsRow($data,$row, $column, $total = 0)
        {
            $respondents = $data->getRespondents();

            if($total)
                return $respondents;
            else
                return count($respondents);
        }

        // called on rendering a grid row
        // Column count all respondents in target audiences
        protected function gridCountRespondentsAll($quiz)
        {
            $quizEl = Quiz::model()->findByPk($quiz);
            return $quizEl->countRespondets();
        }
}
