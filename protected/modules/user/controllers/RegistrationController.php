<?php

class RegistrationController extends Controller
{
	public $defaultAction = 'registration';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
	/**
	 * Registration user
	 */
	public function actionRegistration() {
			// проверка данных формы по одной модели
            $formModel	=	new RegistrationForm;
            $formModel->scenario = 'registration';
            // занесение данных - по другой. ну что поделаешь... жизнь - боль
            $userModel	=	new User;
            $profile	=	new Profile;
            $licenses	=	new Licenses;
            $profile->regMode = true;
            
			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
			{
				echo UActiveForm::validate(array($formModel,$profile));
				Yii::app()->end();
			}

		    if (Yii::app()->user->id) {
		    	$this->redirect(Yii::app()->controller->module->profileUrl);
		    }
		    else {
		    	if(isset($_POST['RegistrationForm'])) {
		    		$formModel->attributes	=	$_POST['RegistrationForm'];

					$userModel->attributes	=	$_POST['RegistrationForm'];
					$profile->attributes	=	$_POST['Profile'];
					$licenses->attributes	=	$_POST['Licenses'];

                    $licenses->tariff = 1;//пока только бесплатная регистрация по состоянию на 12.03.2015

					$userModel->activkey=Yii::app()->controller->module->encrypting(microtime().$userModel->password);

					$profile->user_id=0;

					if($formModel->validate() && $profile->validate()) {
						$password = $userModel->password;
						$userModel->password=Yii::app()->controller->module->encrypting($userModel->password);

						// делаем его менеджером и маркетёром
						$userModel->manager = 1;
						$userModel->marketer = 1;
						$userModel->superuser = 0;

						$userModel->status = 1;

						if($userModel->save()) {
							$profile->user_id=$userModel->getPrimaryKey();
							$profile->save();

							// add first license
							$cost = Tariffs::model()->find(array(
								'select'=>'cost',
								'condition'=>'id=:id',
								'params'=>array(':id'=>$licenses->tariff),
							));

							if ($cost->cost == 0) {
								$licenses->active= Licenses::ACTIVE;
								$licenses->date_open=Date("Y-m-d");
							}

							// when we wait any month
							if ($licenses->tariff->free_month > 0) {
								$licenses->active= Licenses::ACTIVE;
								$licenses->date_expirate=new CDbExpression('DATE_ADD(NOW(), INTERVAL '.$licenses->tariff->free_month.' MONTH)');

							}
							$licenses->user=$userModel->getPrimaryKey();
							$licenses->save();

							// mail to chief
							UserModule::sendMail("vadimshavlukevich@yandex.ru", "Yahoooooo! Bazinga!", "Chief, we have new user!!!<br />His mail: ".$userModel->email);


							if (Yii::app()->controller->module->sendActivationMail) {
								$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $userModel->activkey, "email" => $userModel->email));

								Mailtpl::send("registration", $userModel->email, array("{activation_url}"=>$activation_url,"{mail}"=>$userModel->email,"{password}"=>$password));
							}

							$identity=new UserIdentity($userModel->username, $_POST['RegistrationForm']['password']);
							$identity->authenticate();
							Yii::app()->user->login($identity,0);

							$this->redirect(Yii::app()->controller->module->thankyou);							

							/*
							так было раньше - просто выводилась информация об авторизации
							теперь аутентификация и редирект
							if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
									$identity=new UserIdentity($userModel->username,$soucePassword);
									$identity->authenticate();
									Yii::app()->user->login($identity,0);
									$this->redirect(Yii::app()->controller->module->returnUrl);
							}
							else {
								if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
								} elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
								} elseif(Yii::app()->controller->module->loginNotActiv) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
								} else {
									#Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
								}
								$this->refresh();
							}
							*/
						}
					}
					else {
						$profile->validate();
					}
				}


			    $this->render('/user/registration', array('model'=>$formModel, 'profile'=>$profile, 'licenses'=>$licenses));
		    }
	}





	public function actionThankyou() {
		$user		=	User::model()->findByPk(Yii::app()->user->id);
		$license	=	$user->license[0];

		// лицензия активна - отсылаем на стартовую страницу
		if ($license->active == Licenses::ACTIVE) {
			$url = Yii::app()->controller->module->mainUrl;
		}
		// лицензия не активна - отсылаем на оплату
		else {
			$url = Yii::app()->controller->module->activateUrl;
		}

		$this->render('/user/thankyou', array('url'=>Yii::app()->createUrl($url[0])));
		   
	}





/*************************************/
/*************************************/
	public function actionRegistration_co() {
			// проверка данных формы по одной модели
            $formModel	=	new RegistrationForm;
            $formModel->scenario = 'registration';
            // занесение данных - по другой. ну что поделаешь... жизнь - боль
            $userModel	=	new User;
            $profile	=	new Profile;
            $licenses	=	new Licenses;
            $profile->regMode = true;
            
			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
			{
				echo UActiveForm::validate(array($formModel,$profile));
				Yii::app()->end();
			}

		    if (Yii::app()->user->id) {
		    	$this->redirect(Yii::app()->controller->module->profileUrl);
		    }
		    else {
		    	if(isset($_POST['RegistrationForm'])) {
		    		$formModel->attributes	=	$_POST['RegistrationForm'];

					$userModel->attributes	=	$_POST['RegistrationForm'];
					$profile->attributes	=	$_POST['Profile'];
					$licenses->attributes	=	$_POST['Licenses'];

					$userModel->activkey=Yii::app()->controller->module->encrypting(microtime().$userModel->password);

					$profile->user_id=0;

					if($formModel->validate() && $profile->validate()) {
						$userModel->password=Yii::app()->controller->module->encrypting($userModel->password);

						// делаем его менеджером и маркетёром
						$userModel->manager = 1;
						$userModel->marketer = 1;
						$userModel->superuser = 0;

						$userModel->status = 1;

						if($userModel->save()) {
							$profile->user_id=$userModel->getPrimaryKey();
							$profile->save();

							// add first license
							$cost = Tariffs::model()->find(array(
								'select'=>'cost',
								'condition'=>'id=:id',
								'params'=>array(':id'=>$licenses->tariff),
							));

							if ($cost->cost == 0) {
								$licenses->active= Licenses::ACTIVE;
								$licenses->date_open=Date("Y-m-d");
							}

							// when we wait any month
							if ($licenses->tariff->free_month > 0) {
								$licenses->active= Licenses::ACTIVE;
								$licenses->date_expirate=new CDbExpression('DATE_ADD(NOW(), INTERVAL '.$licenses->tariff->free_month.' MONTH)');

							}
							$licenses->user=$userModel->getPrimaryKey();
							$licenses->save();

							// mail to chief
							UserModule::sendMail("vadimshavlukevich@yandex.ru", "Yahoooooo! Bazinga!", "Chief, we have new user!!!<br />His mail: ".$userModel->email);


							if (Yii::app()->controller->module->sendActivationMail) {
								$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $userModel->activkey, "email" => $userModel->email));

								Mailtpl::send("registration", $userModel->email, array("{activation_url}"=>$activation_url,"{mail}"=>$userModel->email,"{password}"=>$password));
							}

							$identity=new UserIdentity($userModel->username, $_POST['RegistrationForm']['password']);
							$identity->authenticate();
							Yii::app()->user->login($identity,0);

							$this->redirect(Yii::app()->controller->module->thankyou);							

							/*
							так было раньше - просто выводилась информация об авторизации
							теперь аутентификация и редирект
							if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
									$identity=new UserIdentity($userModel->username,$soucePassword);
									$identity->authenticate();
									Yii::app()->user->login($identity,0);
									$this->redirect(Yii::app()->controller->module->returnUrl);
							}
							else {
								if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
								} elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
								} elseif(Yii::app()->controller->module->loginNotActiv) {
									Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
								} else {
									#Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
								}
								$this->refresh();
							}
							*/
						}
					}
					else {
						$profile->validate();
					}
				}


			    $this->render('/user/registration_co', array('model'=>$formModel, 'profile'=>$profile, 'licenses'=>$licenses));
		    }
	}
}