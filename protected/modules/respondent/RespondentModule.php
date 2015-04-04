<?php

class RespondentModule extends CWebModule
{
/**
	 * @var int
	 * @desc items on page
	 */
	public $respondent_page_size = 10;

	/**
	 * @var int
	 * @desc items on page
	 */
	public $fields_page_size = 10;

        /**
	 * @var string
	 * @desc hash method (md5,sha1 or algo hash function http://www.php.net/manual/en/function.hash.php)
	 */
	public $hash='md5';

        public $tableRespondents = '{{respondents}}';
	public $tableSessions = '{{sessions}}';
	public $tableRespondentsStatuses = '{{respondents_statuses}}';
	public $tableRespondentsPayments = '{{respondents_payments}}';
	public $tableRespondentsEducations = '{{link_respondents_educations}}';
	public $tableRespondentsGroup = '{{group_respondents}}';
	public $tableRespondentsGroupUsers = '{{link_users_group_respondents}}';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'respondent.models.*',
			'respondent.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}

        /**
	 * @param $str
	 * @param $params
	 * @param $dic
	 * @return string
	 */
	public static function t($str='',$params=array(),$dic='respondent') {
		if (Yii::t("UserModule", $str)==$str)
		    return Yii::t("RespondentModule.".$dic, $str, $params);
        else
            return Yii::t("RespondentModule", $str, $params);
	}

	/**
	 * @return hash string.
	 */
	public static function encrypting($string="") {
		$hash = Yii::app()->getModule('respondent')->hash;
		if ($hash=="md5")
			return md5($string);
		if ($hash=="sha1")
			return sha1($string);
		else
			return hash($hash,$string);
	}

        /**
	 * Return safe respondent data.
	 * @param respondent id not required
	 * @return respondent object or false
	 */
	public static function respondent($id=0) {
            if ($id) {
                return Respondent::model()->findbyPk($id);
            } else return false;
	}

	/**
	 * Return safe respondent data.
	 * @param respondent id not required
	 * @return respondent object or false
	 */
	public function respondents() {
		return Respondent;
	}

        /**
	 * Send mail method
	 */
	public static function sendMail($email,$subject,$message) {
            $adminEmail = Yii::app()->params['adminEmail'];
	    $headers = "MIME-Version: 1.0\r\nFrom: $adminEmail\r\nReply-To: $adminEmail\r\nContent-Type: text/html; charset=utf-8";
	    $message = wordwrap($message, 70);
	    $message = str_replace("\n.", "\n..", $message);
	    return mail($email,'=?UTF-8?B?'.base64_encode($subject).'?=',$message,$headers);
	}

        /**
	 * Send push notifications
	 */
        public static function sendNotifications($messenge = '', $userTokenArray = array(), $badge = 1){
            if(count($userTokenArray) > 0){
                $easyapns = Yii::app()->getModule('apns');
                $easyapns->CreateMessage($userTokenArray);
                $easyapns->AddMessage($messenge);
                $easyapns->AddMessageBadge($badge);
                $easyapns->AddMessageSound();
                $easyapns->PushMessages();
            }
        }
}
