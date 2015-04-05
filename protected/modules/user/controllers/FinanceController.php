<?php

class FinanceController extends Controller {

	public $layout='//layouts/column2';


	public function actionIndex() {
		$user		=	User::model()->findByPk(Yii::app()->user->id);
		$license	=	$user->license[0];
		$extend		=	ExtendLicense::model()->findByPk($license->id);
		$change		=	new ChangeLicense;

		// extension license
		if(isset($_POST['ExtendLicense'])) {
			$extend->months=$_POST['ExtendLicense']['months'];

			if ($extend->save()) {
				Yii::app()->user->setFlash('success', Yii::t('app', 'License succesfully extended'));
				$this->redirect(array('/user/finance'));
			}
		}

		// change license
		if(isset($_POST['ChangeLicense'])) {
			$change->attributes=$_POST['ChangeLicense'];

			if ($change->save()) {
				Yii::app()->user->setFlash('success', Yii::t('app', 'Tariff succesfully changed'));
				$this->redirect(array('/user/finance'));
			}
		}

	    $this->render('index',array(
	    	'license'=>$license,
	    	'extend'=>$extend,
	    	'change'=>$change,
	    	'user'=>$user
	    ));
	}

	public function actionPay() {
		$user		=	User::model()->findByPk(Yii::app()->user->id);
		$license	=	$user->license[0];

		// Будет ли оплата лицензии
		$paylicense = false;
        if(isset($_GET['type']) && $_GET['type'] == "success" && isset($_POST['Shp_1']))
        {
            $quiz_id = $_POST['Shp_1'];
            Yii::app()->user->setFlash('pay_suc', "Оплата прошла успешно");
            $this->redirect('/quiz/'.$quiz_id.'/collection');
        }

		if (isset($_GET['type']) && $_GET['type'] == "success") {
			$extend = ExtendLicense::model()->findByPk($license->id);
			$extend->months = $license->limits->minimum;
			echo "<!-- lid: ".$license->id." -->";
			echo "<!-- months: ".$license->limits->minimum." -->";
			if ($extend->save()) {
				echo "<!-- success -->";
			}
			else {
				echo "<!-- fail -->";
			}

			$this->render('success', array(
			));
		}
		else {
			if ($license->active == Licenses::ACTIVE) {
				$model	=	new Transactions;

				if ($_GET['type'] == "success") {
					Yii::app()->user->setFlash('success', Yii::t('app', 'Pay successfully finished'));
				}
				else if ($_GET['type'] == "fail") {
					Yii::app()->user->setFlash('warning', Yii::t('app', 'Pay failed finished'));
				}

				if(isset($_POST['Transactions'])) {
		        	$model->attributes	=	$_POST['Transactions'];
		        	$model->user = Yii::app()->user->id;
		        	$model->status = Transactions::STATUS_CREATED;
		        	$model->date_open = Date("Y-m-d h:i:s");

		        	if($model->validate()) {
		        		$model->save();
		        	}
				}
			}
			// линцезия не активна - просим автоматически пополнить
			else {
				$model	=	new Transactions;
				$model->summ = $license->limits->cost*$license->limits->minimum;
		        $model->user = Yii::app()->user->id;
		        $model->status = Transactions::STATUS_CREATED;
		        $model->date_open = Date("Y-m-d h:i:s");
		        $model->save();

		        $paylicense = true;
			}

		    $this->render('pay', array(
		    	'model'=>$model,
		    	'paylicense'=>$paylicense
		    ));
		}
	}

	public function actionSub() {
		$dataProvider=new CActiveDataProvider('User', array(
			'criteria'=>array(
				'condition'=>'subfor='.Yii::app()->user->id,
			),

			'pagination'=>array(
				'pageSize'=>5,
			),
		));

		$this->render('sub',array(
			'dataProvider'=>$dataProvider
		));
	}

	public function actionCreatesub() {
		$userModel	=	new User;
		$profile	=	new Profile;
		$formModel	=	new RegistrationForm;
		$profile->regMode = true;


		if(isset($_POST['RegistrationForm'])) {
			$formModel->attributes	=	$_POST['RegistrationForm'];
			$formModel->scenario	=	"addsubuser";

			$userModel->attributes	=	$_POST['RegistrationForm'];
			$profile->attributes	=	$_POST['Profile'];

			$userModel->activkey=Yii::app()->controller->module->encrypting(microtime().$userModel->password);

			$profile->user_id=0;

			if($formModel->validate() && $profile->validate()) {
				$password				=	SubStr(Md5(Rand(1, Time())), 0, 6);
				$userModel->password	=	$password;
				$userModel->password	=	Yii::app()->controller->module->encrypting($userModel->password);

				// äåëàåì åãî ìåíåäæåðîì è ìàðêåò¸ðîì
				$userModel->manager = 1;
				$userModel->marketer = 1;
				$userModel->superuser = 0;
				$userModel->status = 1;
				$userModel->subfor = Yii::app()->user->id;

				if($userModel->save()) {
					$profile->user_id=$userModel->getPrimaryKey();
					$profile->save();

					if (Yii::app()->controller->module->sendActivationMail) {
						UserModule::sendMail($userModel->email, UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->name)), UserModule::t("Inform mail for sub sub user",array('{url}'=>Yii::app()->name,'{login}'=>$userModel->username,'{password}'=>$password)) );
					}

					Yii::app()->user->setFlash('success', UserModule::t('New sub user successfully created'));
					$this->refresh();
				}
			}
			else {
				$profile->validate();
			}
		}

		$this->render('createsub', array(
			'model'=>$formModel,
			'profile'=>$profile
		));

	}

	public function actionDeletesub() {
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$model = User::model()->notsafe()->findbyPk($_GET['id']);;
			$profile = Profile::model()->findByPk($model->id);
			$profile->delete();
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('/user/finance/sub'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

    public function actionRequestForQuestionnaire()
    {
        $quiz_id = $_POST['quiz_id'];
        $quizModel = Quiz::model()->findByPk($quiz_id);
        var_dump($quizModel);
    }
}