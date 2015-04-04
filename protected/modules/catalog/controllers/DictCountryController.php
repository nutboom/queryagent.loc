<?php

class DictCountryController extends Controller
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
                                'actions'=>array('index','view','admin'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','update','delete','cities'),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new DictCountry;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DictCountry']))
		{
			$model->attributes=$_POST['DictCountry'];
			if($model->save())
                            $this->refresh();
				//$this->redirect(array('/catalog/dictCity'));
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

		if(isset($_POST['DictCountry']))
		{
			$model->attributes=$_POST['DictCountry'];
			if($model->save())
                            $this->refresh();
				//$this->redirect(array('/catalog/dictCity'));
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
			$model = $this->loadModel($id);
			$model->is_deleted = 1;
			$model->save();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/catalog/dictCity'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex() {
		$dataProvider=new CActiveDataProvider('DictCountry', array('criteria'=>array('condition' => 'is_deleted=0')));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

        /**
	 * Lists all cities for models.
	 */
	public function actionCities()
	{
            $resp = array();
            if($_GET['TargetAudience']['countries'][0])
                $resp['country'] = $_GET['TargetAudience']['countries'][0];

            if($_GET['TargetAudience']['checked']) {

                $targetAudience = TargetAudience::model()->findByPk($_GET['audience_id']);

                $criteria = new CDbCriteria();
                $criteria->addInCondition('dict_country_id', array_values($_GET['TargetAudience']['countries']));
                $countries = DictCountry::model()->findAll($criteria);

                $data = array();
                foreach ($countries as $i=>$country){
                    $data += CHtml::listData($country->cities,'dict_city_id','title');
                }

                if($targetAudience)
                    $cities = $targetAudience->cities;
                else
                    $cities = array();

                $resp['text'] = CHtml::checkBoxList('TargetAudience[cities]['.($resp['country']-1).']',array_keys($cities), $data, array(
                    'separator'=>'',
                    'container'=>'',
                    'class'=>'TargetAudience_elements_cities',
                    'template'=>'<label class="checkbox">{input} {label}</label>',
                    'labelOptions'=>array('class'=>'inline_with_checkbox'),
                ));


            }
            echo json_encode($resp);
        }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DictCountry('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DictCountry']))
			$model->attributes=$_GET['DictCountry'];

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
		$model=DictCountry::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='dict-country-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
