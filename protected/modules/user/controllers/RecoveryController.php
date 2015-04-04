<?php

class RecoveryController extends Controller
{
	public $defaultAction = 'recovery';

	/**
	 * Recovery password
	 */
	public function actionRecovery () {
		$form = new UserRecoveryForm;
		if (Yii::app()->user->id) {
		    $this->redirect(Yii::app()->controller->module->returnUrl);
		}
		else {
			$email = ((isset($_GET['email']))?$_GET['email']:'');
			$activkey = ((isset($_GET['activkey']))?$_GET['activkey']:'');

			// форма смены пароля
			if ($email&&$activkey) {
				$form2 = new UserChangePassword;
		    	$find = User::model()->notsafe()->findByAttributes(array('email'=>$email));

		    	if(isset($find)&&$find->activkey==$activkey) {
			    	if(isset($_POST['UserChangePassword'])) {
						$form2->attributes=$_POST['UserChangePassword'];

						if($form2->validate()) {
							$find->password = Yii::app()->controller->module->encrypting($form2->password);
							$find->activkey=Yii::app()->controller->module->encrypting(microtime().$form2->password);

							if ($find->status==0) {
								$find->status = 1;
							}

							$find->save();
							Yii::app()->user->setFlash('recoveryMessage',UserModule::t("New password is saved."));
							$this->redirect(Yii::app()->controller->module->recoveryUrl);
						}
					}

					$this->render('changepassword',array('model'=>$form2));
		    	}
		    	else {
		    		Yii::app()->user->setFlash('recoveryMessage',UserModule::t("Incorrect recovery link."));
					$this->redirect(Yii::app()->controller->module->recoveryUrl);
		    	}
		    }
		    // форма ввода email || name
		    else {
			    if(isset($_POST['UserRecoveryForm'])) {
			    	$form->attributes=$_POST['UserRecoveryForm'];

			    	if($form->validate()) {
			    		$user = User::model()->notsafe()->findbyPk($form->user_id);
						$activation_url = 'http://' . $_SERVER['HTTP_HOST'].$this->createUrl(implode(Yii::app()->controller->module->recoveryUrl),array("activkey" => $user->activkey, "email" => $user->email));

						Mailtpl::send("forgot_password", $user->email, array("{activation_url}"=>$activation_url));

						Yii::app()->user->setFlash('recoveryMessage',UserModule::t("Please check your email. An instructions was sent to your email address."));
			    		$this->refresh();
			    	}
			    }
		    	$this->render('recovery',array('model'=>$form));
		    }
		}
	}

}