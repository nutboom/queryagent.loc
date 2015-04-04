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
				'actions'=>array('index','create','update','delete','CreateByLink','useomi','Omigetcountrespondents','Omigetcities','Omisaveaud','Omiedit','Omidelete'),
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

    public function actionCreateByLink($quiz)
    {
        $quizEl = $this->loadModelQuiz($quiz);
        $quizEl->by_link = true;
        $quizEl->state = Quiz::STATE_EDIT;
        $quizEl->save();
        $this->redirect(array('quiz/'.$quiz.'/collection'));
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
		$omiBase = OmiTargetAudience::haveOmiAudience($quizEl->quiz_id);

		if (($quizEl->state == Quiz::STATE_WORK || $quizEl->state == Quiz::STATE_MODERATION) && ($base || $omiBase)) {
       		$addButton = false;
		}

		$dataProvider=new CActiveDataProvider('TargetAudience', array(
                    'criteria'=>array(
                        'condition'=>'quiz_id='.$quiz,
                    ),
		));

		$omiAud = OmiTargetAudience::model()->findAllByAttributes(array('quiz_id'=>$quizEl->quiz_id));
		$user = User::model()->findByPk(Yii::app()->user->id);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
                        'quiz'=>$quizEl,
                        'addButton'=>$addButton,
            'omiAud'=>$omiAud,
            'user'=>$user,
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

        public function actionUseomi($quiz)
    {
        $quizEl = Quiz::model()->findByPk($quiz);
        $targetAud = new OmiTargetAudience();
        $targetAud->quiz_id = $quizEl->quiz_id;
        //$targetAud->save(false);

        $connection=Yii::app()->db;
        $sql = "SELECT * FROM tbl_omi_education";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $education = array();
        while($education[] = $data->read()){};
        unset($education[count($education)-1]);

        $sql = "SELECT * FROM tbl_omi_job_sphere";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $job_sphere = array();
        while($job_sphere[] = $data->read()){};
        unset($job_sphere[count($job_sphere)-1]);

        $sql = "SELECT * FROM tbl_omi_income_evaluation";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $evaluation = array();
        while($evaluation[] = $data->read()){};
        unset($evaluation[count($evaluation)-1]);

        $sql = "SELECT * FROM tbl_omi_city_size";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $citysize = array();
        while($citysize[] = $data->read()){};
        unset($citysize[count($citysize)-1]);

        $sql = "SELECT * FROM tbl_omi_regions";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $regions = array();
        while($regions[] = $data->read()){};
        unset($regions[count($regions)-1]);








        $this->render('useomi', array('quiz'=>$quizEl,
            'model'=>$targetAud,
            'education'=>$education,
            'job_sphere'=>$job_sphere,
            'evaluation'=>$evaluation,
            'citysize'=>$citysize,
            'regions'=>$regions,
        ));
    }

    public function countOmiRespondents()
    {
        $connection=Yii::app()->db;
        $sql = "SELECT count(*) as count_r FROM tbl_omi_respondents";
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $result = $data->read();
        return $result['count_r'];
    }

    public function actionOmigetcountrespondents()
    {
        $age_from = $_POST['age_from'];
        $age_to = $_POST['age_to'];
        $sex = $_POST['sex'];
        $education = isset($_POST['education'])?$_POST['education']:false;
        $jobsphere = isset($_POST['jobsphere'])?$_POST['jobsphere']:false;
        $evaluation = isset($_POST['evaluation'])?$_POST['evaluation']:false;
        $citysize = isset($_POST['citysize'])?$_POST['citysize']:false;
        $regions = isset($_POST['regions'])?$_POST['regions']:false;
        $cities = isset($_POST['cities'])?$_POST['cities']:false;
        $sql_parts = array();
        if($age_from != 0)
        {
            $birth_from = 365*$age_from;
            $sql_parts['birth_from'] = "birthday <= DATE_SUB(NOW(), INTERVAL ".$birth_from." DAY)";
        }

        if($age_to != 0)
        {
            $birth_to = 365*$age_to;
            $sql_parts['birth_to'] = "birthday >= DATE_SUB(NOW(), INTERVAL ".$birth_to." DAY)";
        }

        if($sex != 0) $sql_parts['sex'] = "sex = '".$sex."'";
        if($education) $sql_parts['education'] = "education in (".implode(',',$education).")";
        if($jobsphere) $sql_parts['jobsphere'] = "jobsphere in (".implode(',',$jobsphere).")";
        if($evaluation) $sql_parts['evaluation'] = "evaluation in (".implode(',',$evaluation).")";
        if($citysize) $sql_parts['citysize'] = "citysize in (".implode(',',$citysize).")";
        if($regions) $sql_parts['regions'] = "region in (".implode(',',$regions).")";
        if($cities) $sql_parts['cities'] = "homecity in (".implode(',',$cities).")";

        $sql = "SELECT  count(*) as count_r FROM tbl_omi_respondents";
        if (count($sql_parts)>0)
        {
            $i = 1;
            $sql .= " WHERE ";
            foreach($sql_parts as $value)
            {
                $sql .= $value;
                if($i < count($sql_parts)) $sql .= " AND ";
                ++$i;
            }
        }

        $connection=Yii::app()->db;
        $command = $connection->createCommand($sql);
        $data = $command->query();
        $result = $data->read();
        echo $result['count_r'];


    }

    public function actionOmigetcities()
    {
        $regions = isset($_POST['regions'])?$_POST['regions']:false;
        if($regions)
        {
            $sql = "SELECT * FROM tbl_omi_cities WHERE region_id IN (".implode(',',$regions).")";
            $connection=Yii::app()->db;
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $regions = array();
            while($regions[] = $data->read()){};
            unset($regions[count($regions)-1]);
            $result = 'Город:<br><select name="cities[]" multiple class="selectpicker" data-header="Выберите город" data-selected-text-format="count>2" data-live-search="true" data-container="body" data-size="10">';
            foreach($regions as $item)
            {
                if (isset($_POST['audModel_id']))
                {
                    $audModel = OmiTargetAudience::model()->findByPk($_POST['audModel_id']);
                    $selected = in_array($item['id'], explode(',',$audModel->city))?'selected':'';
                    $result .= "<option ".$selected." value='".$item['id']."'>".$item['title']."</option>";
                }
                else
                $result .= "<option value='".$item['id']."'>".$item['title']."</option>";
            }
            $result .= "</select>";
            echo $result;
        }
        else echo 0;
    }

    public function actionOmisaveaud()
    {
        $quiz_id = $_POST['quiz_id'];
        $age_from = $_POST['age_from'];
        $age_to = $_POST['age_to'];
        $sex = $_POST['sex'];
        $respondents_count = $_POST['respondents_count'];
        $education = isset($_POST['education'])?$_POST['education']:false;
        $jobsphere = isset($_POST['jobsphere'])?$_POST['jobsphere']:false;
        $evaluation = isset($_POST['evaluation'])?$_POST['evaluation']:false;
        $citysize = isset($_POST['citysize'])?$_POST['citysize']:false;
        $regions = isset($_POST['regions'])?$_POST['regions']:false;
        $cities = isset($_POST['cities'])?$_POST['cities']:false;
        $limit = isset($_POST['limit'])?$_POST['limit']:false;

        if(isset($_POST['audModel_id']))
            $aud = OmiTargetAudience::model()->findByPk($_POST['audModel_id']);
        else
            $aud  = new OmiTargetAudience();
        $aud->quiz_id = $quiz_id;
        $aud->age_from = $age_from;
        $aud->age_to = $age_to;
        $aud->sex = $sex;
        $aud->respondents_count = $respondents_count;
        if($education) $aud->education = implode(',',$education); else $aud->education = '';
        if($jobsphere) $aud->jobsphere = implode(',',$jobsphere); else $aud->jobsphere = '';
        if($evaluation) $aud->evaluation = implode(',',$evaluation); else $aud->evaluation = '';
        if($citysize) $aud->citysize = implode(',',$citysize); else $aud->citysize = '';
        if($regions) $aud->region = implode(',',$regions); else $aud->region = '';
        if($cities) $aud->city = implode(',',$cities); else $aud->city = '';
        if($limit) $aud->limit = $limit;
        $aud->save(false);
        $this->redirect('index');

    }

    public function actionOmiedit($aud_id)
    {
        $audModel = OmiTargetAudience::model()->findByPk($aud_id);

        if($audModel)
        {
            $quizEl = Quiz::model()->findByPk($audModel->quiz_id);

            $connection=Yii::app()->db;
            $sql = "SELECT * FROM tbl_omi_education";
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $education = array();
            while($education[] = $data->read()){};
            unset($education[count($education)-1]);

            $sql = "SELECT * FROM tbl_omi_job_sphere";
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $job_sphere = array();
            while($job_sphere[] = $data->read()){};
            unset($job_sphere[count($job_sphere)-1]);

            $sql = "SELECT * FROM tbl_omi_income_evaluation";
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $evaluation = array();
            while($evaluation[] = $data->read()){};
            unset($evaluation[count($evaluation)-1]);

            $sql = "SELECT * FROM tbl_omi_city_size";
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $citysize = array();
            while($citysize[] = $data->read()){};
            unset($citysize[count($citysize)-1]);

            $sql = "SELECT * FROM tbl_omi_regions";
            $command = $connection->createCommand($sql);
            $data = $command->query();
            $regions = array();
            while($regions[] = $data->read()){};
            unset($regions[count($regions)-1]);








            $this->render('omiedit', array('quiz'=>$quizEl,
                'audModel'=>$audModel,
                'education'=>$education,
                'job_sphere'=>$job_sphere,
                'evaluation'=>$evaluation,
                'citysize'=>$citysize,
                'regions'=>$regions,
            ));
        }
    }

    public function actionOmidelete($aud_id)
    {
        OmiTargetAudience::model()->deleteByPk($aud_id);
        $this->redirect('index');
    }
}
