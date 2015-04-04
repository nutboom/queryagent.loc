<?php

class AdminController extends Controller
{
	public $defaultAction = 'admin';
	public $layout='//layouts/column2';

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
			array('deny', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete','create','update','view','clients'),
				'users'=>UserModule::getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['User']))
            $model->attributes=$_GET['User'];

        $this->render('index',array(
            'model'=>$model,
        ));
		/*$dataProvider=new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->controller->module->user_page_size,
			),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));//*/
	}


	/**
	 * Displays a particular model.
	 */
	public function actionView() {
		$model	= $this->loadModel();
		$license= new Licenses;

		//$this->performAjaxValidation($license);

		if(isset($_POST['Licenses'])) {
			$license->attributes=$_POST['Licenses'];

			$license->user = $model->id;

			if($license->validate()) {
				$license->save();
				Yii::app()->user->setFlash('success', Yii::t('app','License successfully created'));
			}
		}

		$dataProviderLicenses=new CActiveDataProvider('Licenses', array(
			'criteria'=>array(
				'condition'=>'user='.$model->id,
			),

			'pagination'=>array(
				'pageSize'=>5,
			),
		));

		$license->date_open = Date("Y-m-d");
		$license->date_expirate = Date("Y-m-d");

		$this->render('view',array(
			'model'=>$model,
			'license'=>$license,
			'dataProviderLicenses'=>$dataProviderLicenses
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;
		$profile=new Profile;
		$this->performAjaxValidation(array($model,$profile));
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->activkey=Yii::app()->controller->module->encrypting(microtime().$model->password);
			$profile->attributes=$_POST['Profile'];
			$profile->user_id=0;
			if($model->validate() && $profile->validate()) {
				$model->password=Yii::app()->controller->module->encrypting($model->password);
                                $model->manager = $model->superuser ? 1 : $model->manager;
                                $client_id = $_POST['User']['client'];
				if($model->save()) {
					$profile->user_id=$model->getPrimaryKey();
					$profile->save();
                                        if(!$model->superuser && !$model->manager && $client_id)
                                            Yii::app()->db->createCommand('INSERT INTO tbl_users_clients(users_id, clients_id) VALUES(:user,:client)')->bindParam(":user",$model->getPrimaryKey())->bindParam(":client",$client_id)->execute();
				}
				//$this->redirect(array('view','id'=>$model->id));
                                $this->refresh();
			} else $profile->validate();
		}

		$this->render('create',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate() {
		$model=$this->loadModel();
		$profile=$model->profile;

		$this->performAjaxValidation(array($model,$profile));
		if(isset($_POST['User'])) {
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];

			if($model->validate()&&$profile->validate()) {
				$old_password = User::model()->notsafe()->findByPk($model->id);
				if ($old_password->password!=$model->password) {
					$model->password=Yii::app()->controller->module->encrypting($model->password);
					$model->activkey=Yii::app()->controller->module->encrypting(microtime().$model->password);
				}

				$client_id = $_POST['User']['client'];
				$model->save();
				$profile->save();

				if (!$model->superuser && !$model->manager && $client_id) {
					if (count($model->client)) {
						Yii::app()->db->createCommand()->update('{{users_clients}}', array('clients_id'=>$client_id), 'users_id=:user', array(':user'=>$model->id));
					}
					else {
						Yii::app()->db->createCommand()->insert('{{users_clients}}', array('users_id'=>$model->id, 'clients_id'=>$client_id,));
					}
				}
				else {
					Yii::app()->db->createCommand()->delete('{{users_clients}}', 'users_id=:user', array(':user'=>$model->id));
				}

				//$this->redirect(array('view','id'=>$model->id));
				//$this->refresh();
			}
			else {
				$profile->validate();
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'profile'=>$profile
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			$profile = Profile::model()->findByPk($model->id);
			$profile->delete();
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('/user'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

        /**
	 * Creates connect between a model and user.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionClients()
	{
		$model = $this->loadModel();
		$profile = $model->profile;

                if(Yii::app()->request->isPostRequest){
                    $model->deleteConnectionWithClient();
                    if(isset($_POST['User'])){
                        $model->setClients($_POST['User']['client']);
                        if($model->withRelated->save(true, array('client')))
                        	//$this->redirect(array('/user'));
                                $this->refresh();
                    }
                }

		$this->render('clients',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	/**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($validate)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
        {
            echo CActiveForm::validate($validate);
            Yii::app()->end();
        }
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
				$this->_model=User::model()->notsafe()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

}