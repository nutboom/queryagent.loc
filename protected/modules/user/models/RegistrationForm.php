<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'UserController'.
 */
class RegistrationForm extends User {
	public $verifyPassword;
	public $verifyCode;


	protected function beforeValidate() {
		if(parent::beforeValidate()){
			if($this->isNewRecord && $this->scenario == "addsubuser") {
				$count = current(Yii::app()->db->createCommand()
				->select('count(id)')
				->from('{{users}}')
				->where(
					'subfor = :subfor',
					array(':subfor'=>Yii::app()->user->id)
				)
				->queryRow());

				$user	=	User::model()->findByPk(Yii::app()->user->id)->license[0];
				$max	=	$user->limits->limit_users;

				if (($count + 1) >= $max && $max !== "0") {
					$this->addError("", Yii::t('app', 'The maximum number of subusers', array("{max}" => $max)));
					return false;
				}
			}

			return true;
		}
		else {
			return false;
		}
	}

	public function rules() {
		$rules = array(
			array('username, email, phone_number', 'required'),
			array('password, verifyPassword', 'required', 'on'=>'registration'),
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols)."), 'on'=>'registration'),
			array('email', 'email'),
			array('phone_number', 'length', 'max'=>20),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			//array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect."), 'on'=>'registration')
		);
		if (!isset($_POST['ajax'])) {
			array_push($rules,array('verifyCode', 'captcha', 'allowEmpty'=>!UserModule::doCaptcha('registration'), 'on'=>'registration'));
		}

		return $rules;
	}

}