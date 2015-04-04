<?php

class ProfileController extends Controller
{
	public $defaultAction = 'profile';
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
                'actions'=>array('edit','password','avatar','branding'),
                'users'=>array('@'),
            ),
			array('deny',  // allow all users to perform 'index' and 'view' actions
				'users'=>array('*'),
			),
		);
	}

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
	/**
	 * Shows a particular model.
	 */
	public function actionProfile()
	{
		$model = $this->loadUser();
	    $this->render('profile',array(
	    	'model'=>$model,
			'profile'=>$model->profile,
	    ));
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionEdit()
	{
		$model = $this->loadUser();
		$profile=$model->profile;

		// ajax validator
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form')
		{
			echo UActiveForm::validate(array($model,$profile));
			Yii::app()->end();
		}

		if(isset($_POST['User'])) {
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];

			if($model->validate()&&$profile->validate()) {
				$model->save();
				$profile->save();
                Yii::app()->user->updateSession();
                Yii::app()->user->setFlash('success', Yii::t('app', 'Your profile successfully changed'));
				$this->refresh();
			} else $profile->validate();
		}

		$this->render('edit',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}



	public function actionPassword() {
		$model = new UserChangePassword;

		if (Yii::app()->user->id) {
			if(isset($_POST['UserChangePassword'])) {
				$model->attributes=$_POST['UserChangePassword'];
				if($model->validate()) {
					$new_password = User::model()->notsafe()->findbyPk(Yii::app()->user->id);
					$new_password->password = UserModule::encrypting($model->password);
					$new_password->activkey=UserModule::encrypting(microtime().$model->password);
					$new_password->save();
					Yii::app()->user->setFlash('success',UserModule::t("New password is saved."));
					$this->redirect(array("/user/profile/password"));
				}
			}

			$this->render('password',array(
				'model'=>$model
			));
		}
	}

	public function actionAvatar() {
		if (Yii::app()->user->id) {
			$model = User::model()->findByPk(Yii::app()->user->id);

			if(isset($_FILES['avatar'])) {
				$avatar	=	CUploadedFile::getInstanceByName('avatar');
				$ext	=	$avatar->getExtensionName();
				$name	=	$model->username.'.'.$ext;
				$path	=	Yii::getPathOfAlias('webroot').'/upload/users/'.$name;

				if ($avatar->saveAs($path)) {
					$image = Yii::app()->image->load($path);
					list($width, $height, $type, $attr) = getimagesize($path);
					if($width > 45){
						$image->resize(45, 45);
						$image->save();
					}

					Yii::app()->user->setFlash('success', Yii::t('app','Avatar successfully changed'));

					$model->avatar = $name;
					$model->save();
				}
			}

			$avatar	=	$model->avatar;
			$avatar =	($avatar) ? "/upload/users/".$avatar : "/images/none_avatar.png";

			$this->render('avatar',array(
				'model'=>$model,
				'avatar'=>$avatar
			));
		}
	}


	public function actionBranding() {
		if (Yii::app()->user->id) {
			$model = User::model()->findByPk(Yii::app()->user->id)->branding;

			if (!$model) {
				$model = new Branding;
				$model->user = Yii::app()->user->id;
			}

			if(isset($_POST['Branding'])) {
				$model->attributes=$_POST['Branding'];

				if ($model->validate()) {
					Yii::app()->user->setFlash('success', Yii::t('app', 'Branding successfully changed'));
					$model->save();
				}
			}

			$model->logo =	($model->logo) ? "/upload/branding/".$model->logo : "/images/logo.png";
			$model->logo_social =	($model->logo_social) ? "/upload/branding/".$model->logo_social : "/images/logo.png";

			$this->render('branding', array(
				'model'=>$model
			));
		}
	}

	/**
	 * Change password
	 */
	public function actionChangepassword() {
		$model = new UserChangePassword;
		if (Yii::app()->user->id) {

			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='changepassword-form')
			{
				echo UActiveForm::validate($model);
				Yii::app()->end();
			}

			if(isset($_POST['UserChangePassword'])) {
					$model->attributes=$_POST['UserChangePassword'];
					if($model->validate()) {
						$new_password = User::model()->notsafe()->findbyPk(Yii::app()->user->id);
						$new_password->password = UserModule::encrypting($model->password);
						$new_password->activkey=UserModule::encrypting(microtime().$model->password);
						$new_password->save();
						Yii::app()->user->setFlash('profileMessage',UserModule::t("New password is saved."));
						$this->redirect(array("profile"));
					}
			}
			$this->render('changepassword',array('model'=>$model));
	    }
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadUser()
	{
		if($this->_model===null)
		{
			if(Yii::app()->user->id)
				$this->_model=Yii::app()->controller->module->user();
			if($this->_model===null)
				$this->redirect(Yii::app()->controller->module->loginUrl);
		}
		return $this->_model;
	}
}