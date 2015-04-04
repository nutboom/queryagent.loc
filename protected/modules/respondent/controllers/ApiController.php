<?php

class ApiController extends Controller
{
         public $layout='';

        /**
         * Default response format
         * either 'json' or 'xml'
         */
        private $format = 'json';

        /**
         * @return array action filters
         */
        public function filters()
        {
                return array();
        }

        public function init()
        {
            parent::init();

            /*Yii::app()->attachEventHandler('onError',array($this,'handleError'));
            Yii::app()->attachEventHandler('onException',array($this,'handleError'));*/
            Yii::app()->errorHandler->errorAction='respondent/api/error';

        }

        public function handleError(CEvent $event)
        {
            if ($event instanceof CExceptionEvent) {
              // handle exception
              // ...
            }
            elseif($event instanceof CErrorEvent)
            {
              // handle error
              // ...
            }

            $event->handled = TRUE;
        }

         /*** Actions ***/

        # GET /api/v1/captcha
        public function actionCaptcha()
        {
            $self = new AuthCaptchaApi();
            $code = $self->generateVerifyCode();
            $img_path = $self->renderImage($code);

            Yii::app()->db->createCommand()->insert('{{captcha}}', array(
                'code'=>$code,
                'img_name'=>basename($img_path),
                'full_path'=>$img_path,
                'date_created'=>date('Y-m-d H:i:s'),
                'IP'=>$_SERVER['REMOTE_ADDR'],
            ));

            $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('code' => basename($img_path), 'url' => Yii::app()->request->hostInfo.$img_path)));
            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/authorize/?login=[user-login]&password=[user-password]
        public function actionAutorizeUsers($login, $password, $secret)
        {
            $login = htmlspecialchars(trim($login));
            $password = md5(htmlspecialchars(trim($password)));
            $secret = htmlspecialchars(trim($secret));

            $dateAuth = date('Y-m-d H:i:s');
            $sessionId = '';
            $user = '';
            $jsonText = '';

            if(strlen($login) > 0 && strlen($password) > 0 && strlen($secret) > 0) {
                $user_res = Respondent::model()->find('phone_number=:phone AND password=:password AND (blocked IS NULL OR blocked=0)', array(':phone' => $login, ':password' => $password));
                if($user_res) {
                    $sessionId = md5($secret.date('d.m.Y').time());
                    $user = $user_res['id'];
                    $user_has_money = (bool)$user_res['payable'];

                    $sessionLate = Session::model()->find('respondent_id=:user', array(':user'=>$user));
                    if($sessionLate)
                            $sessionLate->delete();

                    $session = new Session;
                    $session->attributes = array('secret_id' => $secret, 'session_id' => $sessionId, 'datetime' => $dateAuth, 'respondent_id' => $user);

                    if($session->save()){
                        $stepReg = 0;
                        if(!$user_res['phone_is_confirmed'])
                            $stepReg = 1;
                        /*elseif(!$user_res['email_actual'] || $user_res['email_new'])
                            $stepReg = 2;*/
                        elseif($user_has_money && $user_res['sex'] == Respondent::NONE)
                            $stepReg = 3;
                        else
                            $stepReg = 4;

                        $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('user_session' => $sessionId, 'user_id' => $user, 'reg_step' => $stepReg, 'payable' => $user_has_money)));
                    } else
                        $jsonText = CJSON::encode($this->jsonStruct(2, Yii::t('app', 'At the moment you can not login. Please try again later.')));
                } else
                    $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }
            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/register
        public function actionCreateUsers()
        {
            $jsonText = '';

            $phone = htmlspecialchars(trim($_POST['phone']));
            if($phone && preg_match('/7([0-9]{10})/', $phone, $phoneCompare)){
                $existRespondent = Respondent::model ()->find("phone_number LIKE :phone AND (blocked IS NULL OR blocked=0)", array(':phone'=>'%'.$phoneCompare[1]));

                if($existRespondent && $existRespondent['phone_is_confirmed']){
                    $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'Respondent with the phone number already exists. Enter another phone number.')));
                } else {
                    if($this->verificationCaptcha()){
                        if($existRespondent)
                            $respondent = $existRespondent;
                        else
                            $respondent = new Respondent;

                        $user = array();
                        $user['phone_number'] = trim($_POST['phone']);
                        $user['phone_code'] = substr(number_format(time() * rand(),0,'',''),0,5);
                        $user['phone_code_expdate'] = $this->pointEndLifetime(Respondent::PHONE_CODE_LIFETIME);
                        $user['email_new'] = null;
                        $user['email_actual'] = trim($_POST['email']);
                        $user['email_code'] = substr(number_format(time() * rand(),0,'',''),0,5);
                        $user['email_code_expdate'] = $this->pointEndLifetime(Respondent::EMAIL_CODE_LIFETIME);
                        $user['password'] = md5(trim($_POST['password']));
                        $user['first_name'] = trim($_POST['fname']);
                        $user['last_name'] = trim($_POST['lname']);
                        $user['country_id'] = intval($_POST['country']);
                        $user['city_id'] = intval($_POST['city']);
                        $user['payable'] = intval($_POST['payable']);

                        $statusZeroKarma = Status::model()->find('karma=:karma', array(':karma'=>0));
                        $user['state_id'] = $statusZeroKarma['id'];
                        $user['blocked'] = 0;

                        $respondent->attributes = $user;
                        if($respondent->save()) {
                            $this->verificationCaptcha($respondent->getPrimaryKey());

                            //TODO: Удалить code_phone
                            $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('code_phone' => $user['phone_code'])));
                            // ! Отправка смс с кодом подтверждения телефона
                            Utils::send_sms($respondent['phone_number'], Yii::t('app', 'Confirmation code phone').': '.$respondent['phone_code']);
                        } else
                            $jsonText = CJSON::encode($this->jsonStruct(2, Yii::t('app', 'At the moment you can not register. Please try again later.')));
                    } else {
                        $jsonText = CJSON::encode($this->jsonStruct(5, Yii::t('app', 'Captcha incorrectly filled.')));
                    }
                }
            } else {
                $jsonText = CJSON::encode($this->jsonStruct(4, Yii::app()->getModule('respondent')->t('Currently registration is only available to users with phone numbers 7XXXXXXXXXX.')));
            }
            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/profile/device/?&session=[user-session]
        public function actionAddPushNotificationID($session) {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $deviceTOKEN = trim($_POST['token']);
                    $deviceTYPE = (isset($_POST['type'])) ? trim($_POST['type']) : "ios";
                    
                    if(strlen($deviceTOKEN) > 0){
                        $sesssionID->device_token = $deviceTOKEN;
                        $sesssionID->device_type = $deviceTYPE;
                        $sesssionID->update(array('device_token', 'device_type'));
                        $jsonText = CJSON::encode($this->jsonStruct());
                    } else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'Push Notification Token is empty.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/logout/?delete=[delete-type: {token, all}]&session=[user-session]
        public function actionLogoutUsers($delete, $session) {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    switch(htmlspecialchars(trim($delete))){
                        case 'token':
                            $sesssionID->updateByPk($sesssionID['session_id'], array('device_token' => NULL));
                            break;
                        case 'all':
                            $sesssionID->delete();
                            break;
                    }
                    $jsonText = CJSON::encode($this->jsonStruct());
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/sendPush/?session=[user-session]
        public function actionSendPush($session) {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $messenge = Yii::t('app', 'New quiz');
                    Yii::app()->getModule('respondent')->sendNotifications($messenge, array($sesssionID['device_token']));
                    $jsonText = CJSON::encode($this->jsonStruct());
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/profile/changepassword/?passwordprev=[user-prev]&session=[user-session]
        public function actionChangePasswordUsers($passwordprev, $session)
        {
            $passwordprev = htmlspecialchars(trim($passwordprev));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    if($user['password'] == md5($passwordprev)){
                        $user['password'] = md5(trim($_POST['password']));
                        if($user->save()) {
                            $jsonText = CJSON::encode($this->jsonStruct());
                        } else
                            $jsonText = CJSON::encode($this->jsonStruct(2, Yii::t('app', 'At the moment you can not change password. Please try again later.')));
                    } else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::app()->getModule('respondent')->t('Password is not the same as the respondent password.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/profile/newpassword/?phone=[user-phone]
        public function actionNewPasswordUsers($phone)
        {
            $phone = htmlspecialchars(trim($phone));
            if(isset($_POST['code']))
                $code = htmlspecialchars(trim($_POST['code']));
            $jsonText = '';

            if($phone){
                $existRespondent = Respondent::model ()->find('phone_number LIKE :phone AND (blocked IS NULL OR blocked=0)', array(':phone'=>'%'.substr($phone, -10)));
                if($existRespondent){
                    // Если телефон не подтвержден
                    if($existRespondent['phone_is_confirmed']){
                        if(!isset($code)){
                            if($this->verificationCaptcha($existRespondent['id'])){
                            // Confirmation code password
                               $existRespondent['password_code'] = substr(number_format(time() * rand(),0,'',''),0,5);
                            } else {
                                $jsonText = CJSON::encode($this->jsonStruct(5, Yii::t('app', 'Captcha incorrectly filled.')));
                            }
                        } else {
                            if($code == $existRespondent['password_code']){
                                // New password
                                $length = 7;
                                $chars = array_merge(range('a','z'), range('A','Z'), range(0,9));
                                shuffle($chars);
                                $newPassword = implode(array_slice($chars, 0, $length));

                                $existRespondent['password'] = md5($newPassword);
                            } else
                                $jsonText = CJSON::encode($this->jsonStruct(5, Yii::t('app', 'You have entered incorrect code confirm the password.')));
                        }

                        if(!$jsonText){
                            if($existRespondent->save()) {
                                // Отправка пароля sms-сообщением
                                if(!isset($_POST['code'])){
                                    Utils::send_sms($existRespondent['phone_number'], Yii::t('app', 'Confirmation code password').': '.$existRespondent['password_code']);
                                    $jsonText = CJSON::encode($this->jsonStruct());
                                }else{
                                    Utils::send_sms($existRespondent['phone_number'], Yii::t('app', 'New password').': '.$newPassword);
                                    $jsonText = CJSON::encode($this->jsonStruct());
                                }
                            } else
                                $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'At the moment you can not recovery password. Please try again later.')));
                        }
                    } else
                        $jsonText = CJSON::encode($this->jsonStruct(4, Yii::t('app', 'You can not recover the password. Please login to the application using your user name and password or re-register.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/users/profile/confirm/?type=[user-type]&code=[user-code]&session=[user-session]
        public function actionConfirmParam($type, $code, $session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $type = htmlspecialchars(trim($type));
            $code = htmlspecialchars(trim($code));

            $jsonText = '';

            if($sesssionID){
                $respondent = $sesssionID->respondent;

                if(strtotime($respondent[$type.'_code_expdate']) >= strtotime(date('Y-m-d H:i:s'))) {
                    if($respondent[$type.'_code'] == $code) {
                        if($type == 'phone')
                            $respondent['phone_is_confirmed'] = 1;
                        /*elseif($type == 'email'){
                            $respondent['email_actual'] = $respondent['email_new'];
                            $respondent['email_new'] = null;
                        }*/
                        if($respondent->save())
                            $jsonText = CJSON::encode($this->jsonStruct());
                        else
                            $jsonText = CJSON::encode($this->jsonStruct(4, Yii::app()->getModule('respondent')->t('At the moment, you can not confirm the code. Please try again later.')));

                    }else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::app()->getModule('respondent')->t('Confirmation code '.$type.' is incorrect.')));

                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('The period of validity has expired '.$type.' confirmation code.')));

            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/users/profile/code/?type=[code-type]&session=[user-session]
        public function actionCodeParam($type, $session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $type = htmlspecialchars(trim($type));

            $jsonText = '';

            if($sesssionID){
                $respondent = $sesssionID->respondent;

                $access_code = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{history_respondent}}')
                        ->where('respondent_id=:respondent AND key_action="code_phone" AND action_do="edit" AND DATE(date_created)=:dateNow', array(':respondent'=>$respondent['id'],':dateNow'=>date('Y-m-d')))
                        ->queryAll();

                if(!$access_code || count($access_code) < 3){
                    Yii::app()->db->createCommand()->insert('{{history_respondent}}', array(
                        'respondent_id'=>$respondent['id'],
                        'key_action'=>"code_phone",
                        'action_do'=>"edit",
                        'date_created'=>date('Y-m-d H:i:s'),
                    ));

                    $respondent[$type.'_code'] = substr(number_format(time() * rand(),0,'',''),0,5);
                    //$respondent[$type.'_code_expdate'] = $this->pointEndLifetime($type == 'phone' ? Respondent::PHONE_CODE_LIFETIME : Respondent::EMAIL_CODE_LIFETIME);
                    $respondent[$type.'_code_expdate'] = $this->pointEndLifetime(Respondent::PHONE_CODE_LIFETIME);

                    if($respondent->save()){
                        $jsonText = CJSON::encode($this->jsonStruct());
                        // ! Отсылаем смс и электронной почты с новым кодом подтверждения
                        if($type == 'phone')
                            Utils::send_sms($respondent['phone_number'], Yii::t('app', 'Confirmation code phone').': '.$respondent[$type.'_code']);
                        /*elseif($type == 'email')
                            Utils::send_mail($respondent['email_new'], $this->textEMail('activationCode', array('code'=>$respondent[$type.'_code'])), Yii::t('app', 'Confirmation code email'));*/
                    } else
                        $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('At the moment, you can not confirm the code. Please try again later.')));
                } else {
                    $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'Getting a new phone confirmation code is not available, because you have reached the limit of code updates a day.')));
                }
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/users/profile/?session=[user-session]
        public function actionViewUsers($session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $profile = array(
                        "avatar"=>$user["avatar"] ? Yii::app()->request->hostInfo.Respondent::model()->getPathAvatar().$user["avatar"] : null,
                        "fname"=>$user["first_name"],
                        "lname"=>$user["last_name"],
                        "phone"=>$user["phone_number"],
                        "email"=>$user["email_actual"],
                        "birthdate"=>$user["birth_date"],
                        "sex"=>$user["sex"],
                        "education"=>$user->educations ? array_keys($user->educations) : null,
                        "marital_state"=>$user["marital_state"],
                        "scope"=>$user["scope_id"],
                        "position"=>$user["position_id"],
                        "income"=>$user["income"],
                        "state"=>$user["state_id"],
                        "country"=>$user["country_id"],
                        "city"=>$user["city_id"],
                        "money"=>$user["money"],
                        "karma"=>$user["karma"],
                        "payable"=>$user["payable"]
                    );
                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $profile));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/users/profile/balance/?session=[user-session]
        public function actionBalanceUsers($session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $balance = array(
                        "state"=>$user["state_id"],
                        "money"=>$user["money"],
                        "karma"=>$user["karma"]
                    );
                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $balance));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/profile/pay/?to=[user-to]&session=[user-session]
        public function actionPaymetUsers($to, $session)
        {
            $toPay = trim($to);
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $moneyPay = trim($_POST['money']);

                    $criteria = new CDbCriteria();
                    $criteria -> select = 'DATE(t.datetime) AS datetime, sum(money) AS money';
                    $criteria -> condition = 't.respondent_id = :respondentId AND DATE(t.datetime) = DATE(now())';
                    $criteria -> params = array(':respondentId'=>$user['id']);
                    $criteria -> group = 'DATE(t.datetime)';
                    $prevPayments = Payments::model()->findAll($criteria);

                    if((isset($prevPayments[0]['money']) && ($prevPayments[0]['money'] + $moneyPay) <= Payments::$LIMIT_MONEY_DAY) || (!isset($prevPayments[0]['money']) && $moneyPay <= Payments::$LIMIT_MONEY_DAY)){
                        $paymentUser = new Payments;
                        $paymentUser['respondent_id'] = $user['id'];
                        $paymentUser['datetime'] = date('Y-m-d H:i:s');
                        $paymentUser['money'] = $moneyPay;
                        $paymentUser['state'] = Payments::STATE_EXPECT;
                        $paymentUser['type'] = $toPay;

                        if($user['money'] - $moneyPay >= 0){
                            $user['money'] -= $moneyPay;

                            if($paymentUser->save() && $user->save()){
                                $jsonText = CJSON::encode($this->jsonStruct());

                                // ! Выводим деньги на счет мобильного телефона или аккаунт платежной системы QIWI
                            }else
                                $jsonText = CJSON::encode($this->jsonStruct(4, Yii::app()->getModule('respondent')->t('At the moment, you can not withdraw funds. Please try again later.')));
                        }else
                            $jsonText = CJSON::encode($this->jsonStruct(3, Yii::app()->getModule('respondent')->t('Number of money more balance user.')));
                    }else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::app()->getModule('respondent')->t('Limit derived funds for day exceeded.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/users/profile/avatar?session=[user-session]
        public function actionCreateUsersAvatar($session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $avatar = Respondent::getPathAvatar().$this->updatePhoto($user, 'avatar_image', 'image');
                    $user->deleteImage();
                    // go through each uploaded image
                    if(Respondent::saveImage($user->image, Yii::getPathOfAlias('webroot').$avatar))
                        $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('url'=>Yii::app()->request->hostInfo.$avatar)));
                    else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::app()->getModule('respondent')->t('Avatar picture is not saved.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/quizid/[quiz-hash]
        public function actionGetIdByHash($hash, $omi_aud_id) 
        {//
            $hash = htmlspecialchars(trim($hash));
            $quiz = Quiz::model()->find(array(
                'select'=>'*',
                'condition'=>'hash=:hash AND is_deleted = :deleted',
                'params'=>array(':hash'=>$hash, ":deleted"=>Quiz::NO_DELETED),
            ));
            if ($quiz->quiz_id) 
            {
                $result = array(
                        "id" => $quiz->quiz_id,
                        "title" => $quiz->title,
                        "client" => $quiz->anonymous_client ? Yii::t('app', 'Client hide') : $quiz->client['name'],
                        "fill_time" => $quiz->fill_time,
                        "money" => $quiz->money,
                        "karma" => $quiz->karma,
                        "question_count" => $quiz->countQuestions(),
                        "respondents_finished" => $quiz->countApplications(array('condition'=>'state="'.Application::STATE_CLOSE.'" OR state="'.Application::STATE_DONE.'"')),
                        "respondents_started" => $quiz->countApplications(array('condition'=>'state="'.Application::STATE_TODO.'" OR state="'.Application::STATE_REJECT.'" OR state="'.Application::STATE_APPEAL.'"')),
                        "respondents_limit" => $quiz->totalRespondets(),
                        "description" => Quiz::bb($quiz->description),
                        "deadline" => $quiz->deadline ? Utils::pack_date($quiz->deadline) : NULL,
                        "type" => $quiz->type,
                        "state" => $quiz->state,

                        "logo" => Yii::app()->request->hostInfo.Branding::getLogo($quiz->manager_id),
                        "top_color" => Branding::getTopColor($quiz->manager_id),
                        "left_color" => Branding::getLeftColor($quiz->manager_id),
                        "skip_start_page" => $quiz->skip_start_page,

                        );
                if($omi_aud_id)
                {
                    $omiAudModel = OmiTargetAudience::model()->findByPk($omi_aud_id);
                    $appModels = Application::model()->findAllByAttributes(array('quiz_id'=>$quiz->quiz_id, 'omi_aud_id'=>$omiAudModel->id, 'state'=>'close'));
                    if(count($appModels) >= $omiAudModel->limit && $omiAudModel->limit != 0)
                    {
                        $result['quiz_id'] = $quiz->quiz_id;
                        $result['omi_aud_id'] = $omi_aud_id;
                        $result['countappmodels'] = count($appModels);
                        $result['omiaudmodellimit'] = $omiAudModel->limit;
                        $jsonText = CJSON::encode($this->jsonStruct(0, 'Exceeded the limit of the survey', $result));
                    }
                    else
                    {
                        $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));
                    }
                }
                else
                {
                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));
                }

                        
            }
            else
            {
                $jsonText = CJSON::encode($this->jsonStruct(1, 'NTF'));
            }
            
            $this->_sendResponse(200, $jsonText);
            
        }

        # POST /api/v1/users/profile/?session=[user-session]
        public function actionUpdateUsers($session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $profile = CJSON::decode($_POST['user_profile']);

                    if($profile['avatar'])                          $user['avatar'] = basename($profile['avatar']);
                    if($profile['fname'])                           $user['first_name'] = $profile['fname'];
                    if($profile['lname'])                           $user['last_name'] = $profile['lname'];
                    if($profile['phone'])                           $user['phone_number'] = $profile['phone'];
                    if($profile['email'])                           $user['email_actual'] = $profile['email'];
                    if($profile['birthdate'])                       $user['birth_date'] = $profile['birthdate'];
                    if($profile['sex'])                             $user['sex'] = $profile['sex'];

                    $user->deleteConnectionWithEducations();
                    if($profile['education']){
                        $criteria = new CDbCriteria();
                        $criteria->addInCondition('dict_education_id', $profile['education']);
                        $educations = DictEducation::model()->findAll($criteria);

                        $user->educations = $educations;
                    }
                    if($profile['marital_state'])                   $user['marital_state'] = $profile['marital_state'];
                    if($profile['position'])                        $user['position_id'] = $profile['position'];
                    if($profile['scope'])                           $user['scope_id'] = $profile['scope'];
                    if($profile['income'])                          $user['income'] = $profile['income'];
                    if($profile['country'])                         $user['country_id'] = $profile['country'];
                    if($profile['city'])                            $user['city_id'] = $profile['city'];
                    if($profile['payable'])                         $user['payable'] = $profile['payable'];

                    if($user->withRelated->save(true, array('educations')))
                        $jsonText = CJSON::encode($this->jsonStruct());
                    else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'At the moment you can not save profile. Please try again later.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/dictionaries/
        public function actionListDictionaries($since)
        {
            $jsonText = '';

            $educations = Yii::app()->db->createCommand()->select('dict_education_id as id, title, is_deleted')->from(Yii::app()->getModule('catalog')->tableDictEducation)->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->queryAll();
            foreach($educations as $id => $array) {
            	$educations[$id]['is_deleted']	=	($educations[$id]['is_deleted'] == "1") ? true : false;
            	$educations[$id]['id']			=	(integer) $educations[$id]['id'];
            }
            $jobPositions = Yii::app()->db->createCommand()->select('dict_job_position_id AS id, title, is_deleted')->from(Yii::app()->getModule('catalog')->tableDictJobPosition)->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->queryAll();
            foreach($jobPositions as $id => $array) {
            	$jobPositions[$id]['is_deleted']	=	($jobPositions[$id]['is_deleted'] == "1") ? true : false;
            	$jobPositions[$id]['id']				=	(integer) $jobPositions[$id]['id'];
            }
            $scopes = Yii::app()->db->createCommand()->select('dict_scope_id AS id, title, is_job, is_deleted')->from(Yii::app()->getModule('catalog')->tableDictScope)->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->queryAll();
            foreach($scopes as $id => $array) {
            	$scopes[$id]['is_deleted']	=	($scopes[$id]['is_deleted'] == "1") ? true : false;
            	$scopes[$id]['id']		=	(integer) $scopes[$id]['id'];
            }
            $userStates = Yii::app()->db->createCommand()->select('id, title, is_deleted')->from(Status::model()->tableName())->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->queryAll();
            foreach($userStates as $id => $array) {
            	$userStates[$id]['is_deleted']	=	($userStates[$id]['is_deleted'] == "1") ? true : false;
            	$userStates[$id]['id']			=	(integer) $userStates[$id]['id'];
            }
            $countries = Yii::app()->db->createCommand()->select('dict_country_id AS id, title, is_deleted')->from(Yii::app()->getModule('catalog')->tableDictCountry)->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->order('title')->queryAll();
            foreach($countries as $id => $array) {
            	$countries[$id]['is_deleted']	=	($countries[$id]['is_deleted'] == "1") ? true : false;
            	$countries[$id]['id']			=	(integer) $countries[$id]['id'];
            }
            $cities = Yii::app()->db->createCommand()->select('dict_city_id AS id, country_id, title, is_deleted')->from(Yii::app()->getModule('catalog')->tableDictCity)->where('unix_timestamp(last_update) > :since', array(':since'=>$since))->order('title')->queryAll();
            foreach($cities as $id => $array) {
            	$cities[$id]['is_deleted']	=	($cities[$id]['is_deleted'] == "1") ? true : false;
            	$cities[$id]['id']			=	(integer) $cities[$id]['id'];
            	$cities[$id]['country_id']		=	(integer) $cities[$id]['country_id'];
            }

            $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('education' => $educations, 'job_position' => $jobPositions, 'scope' => $scopes, 'user_state' => $userStates, 'country' => $countries, 'city' => $cities)));

            $this->_sendResponse(200, Utils::json_encode_cyr($jsonText));
        }

        # GET /api/v1/quiz/total/?session=[user-session]
        public function actionTotalListQuiz($session)
        {
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $result = array('quiz'=>array("available"=>0, "todo"=>0, "done"=>0),'mission'=>array("available"=>0, "todo"=>0, "done"=>0));

                    $resultQuery = $user->getCountQuiz();

                    foreach ($resultQuery as $id => $value) {
                        if(!$value['state']) {
                            $result[$value['type']][Application::STATE_AVAILABLE] = $value['cquiz'];
                        } else
                            $result[$value['type']][$value['state']] = $value['cquiz'];
                    }

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));

                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/?type=[quiz-type]&state=[quiz-state]&offset=[list-offset]&session=[user-session]
        public function actionListQuiz($session, $offset = null, $type = null, $state = null)
        {
            if($type)
                $type = htmlspecialchars(trim($type)) == 'quiz' ? Quiz::TYPE_GENERAL : Quiz::TYPE_MISSION;
            if($state)
                $state = htmlspecialchars(trim($state));
            if($offset)
                $offset = htmlspecialchars(trim($_GET['offset']));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $condition = array();
                    $params = array();
                    if($type){
                        $condition = array('quiz.type=:typeQuiz AND quiz.is_deleted = :deleted');
                        $params = array(':typeQuiz'=>$type, ":deleted"=>Quiz::NO_DELETED);
                    }
                    if($offset){
                        array_push($condition, 'date(quiz.date_start) <= :offsetDate');
                        $params[':offsetDate'] = $offset;
                    }
                    $quizs = $user->listQuizs($condition, $params);

                    $results['list'] = array();
                    foreach($quizs as $q => $quiz) {
                        $kind = array();
                        if($quiz['money'] && $quiz['money'] > 0)
                            $kind[] = Quiz::KIND_PAID;
                        else
                            $kind[] = Quiz::KIND_FREE;
                        if($user->isEntersGroup($quiz->getIdsGroupsInAudiences()))
                                $kind[] = Quiz::KIND_GROUP;
                        $kind = implode('_', $kind);

                        if ($state == Application::STATE_AVAILABLE){
                            $applications = $quiz->applications(array('condition'=>'respondent_id='.$user['id']));
                            if(!$applications)
                                if((!$quiz['deadline'] && !$quiz['date_stop']) || ($quiz['deadline'] && strtotime($quiz['deadline']) >= strtotime(date('Y-m-d H:i:s'))))
                                    $elemQuizInList = array('id'=>$q, 'title'=>$quiz['title'], 'client'=>$quiz['anonymous_client'] ? Yii::t('app', 'Client hide') : $quiz->client['name'],$quiz->client['name'], 'state'=>$state, 'type'=>$type, 'kind'=>$kind, 'money'=>$quiz['money']);
                        } else{
                            if($state)
                                $applications = $quiz->applications(array('condition'=>'respondent_id='.$user['id'].' AND state="'.$state.'"'));
                            else
                                $applications = $quiz->applications(array('condition'=>'respondent_id='.$user['id']));

                            if($applications)
                                $elemQuizInList = array('id'=>$q, 'title'=>$quiz['title'], 'client'=>$quiz['anonymous_client'] ? Yii::t('app', 'Client hide') : $quiz->client['name'], 'state'=>$applications[current(array_keys($applications))]['state'], 'type'=>$quiz['type'], 'kind'=>$kind, 'money'=>$quiz['money']);
                            // зачем-то выводили анкеты, которых и нет, но заполненном статусе опрсоа
                            /*elseif(!$applications && $quiz->state === Quiz::STATE_FILL)
                                $elemQuizInList = array('id'=>$q, 'title'=>$quiz['title'], 'client'=>$quiz['anonymous_client'] ? Yii::t('app', 'Client hide') : $quiz->client['name'], 'state'=>Application::STATE_CLOSE, 'type'=>$quiz['type'], 'kind'=>$kind, 'money'=>$quiz['money']);
                            */elseif(!$state)
                                $elemQuizInList = array('id'=>$q, 'title'=>$quiz['title'], 'client'=>$quiz['anonymous_client'] ? Yii::t('app', 'Client hide') : $quiz->client['name'], 'state'=>Application::STATE_AVAILABLE, 'type'=>$quiz['type'], 'kind'=>$kind, 'money'=>$quiz['money']);
                        }
                        if(isset($elemQuizInList) && $elemQuizInList)
                            $results['list'][$elemQuizInList['id']] = $elemQuizInList;
                    }

                    $results['offset'] = $results['list'] ? $quiz['date_start'] : null;
                    $results['last'] = $results['list'] ? 1 : null;

                    $results['list'] = array_values($results['list']);

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $results));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/[quiz-id]/?session=[user-session]
        public function actionViewQuiz($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $quiz = Quiz::model()->findByPk($quiz_id);
                    $applicationRespondentQuiz = $quiz->getApplication($user['id']);

                    $kind = array();
                    if($quiz['money'] && $quiz['money'] > 0)
                        $kind[] = Quiz::KIND_PAID;
                    else
                        $kind[] = Quiz::KIND_FREE;
                    if($user->isEntersGroup($quiz->getIdsGroupsInAudiences()))
                            $kind[] = Quiz::KIND_GROUP;
                    $kind = implode('_', $kind);

                    $result = array(
                        "id" => $quiz_id,
                        "title" => $quiz['title'],
                        "client" => $quiz['anonymous_client'] ? Yii::t('app', 'Client hide') : $quiz->client['name'],
                        "fill_time" => $quiz['fill_time'],
                        "money" => $quiz['money'],
                        "karma" => $quiz['karma'],
                        "question_count" => $quiz->countQuestions(),
                        "respondents_finished" => $quiz->countApplications(array('condition'=>'state="'.Application::STATE_CLOSE.'" OR state="'.Application::STATE_DONE.'"')),
                        "respondents_started" => $quiz->countApplications(array('condition'=>'state="'.Application::STATE_TODO.'" OR state="'.Application::STATE_REJECT.'" OR state="'.Application::STATE_APPEAL.'"')),
                        "respondents_limit" => $quiz->totalRespondets(),
                        "description" => Quiz::bb($quiz['description']),
                        "deadline" => $quiz['deadline'] ? Utils::pack_date($quiz['deadline']) : NULL,
                        "type" => $quiz['type'],
                        "needs_confirmation" => $quiz['needs_confirmation'],
                        "kind" => $kind
                    );

                    if($applicationRespondentQuiz)
                        $result["state"] = $applicationRespondentQuiz['state'];
                    else{
                        if($quiz['state'] == Quiz::STATE_FILL)
                            $result["state"] = Application::STATE_CLOSE;
                        else
                            $result["state"] = Application::STATE_AVAILABLE;
                    }

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/[quiz-id]/comments/?session=[user-session]
        # list of comments [GET]
        public function actionListCommentsQuiz($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $quiz = Quiz::model()->with('comments')->findByPk($quiz_id);
                    $result['comments'] = array();
                    foreach($quiz->comments as $c => $comment){
                        $elemComment = array(
                            "id" => $comment['id'],
                            "quiz_id" => $quiz_id,
                            "sender" => $comment->respondent->fullName,
                            "text" => $comment['text'],
                            "datetime" => $comment['date_created']
                        );

                        array_push($result['comments'], $elemComment);
                    }

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/quiz/[quiz-id]/comments/?text=[comment-text]
        # create new comment to quiz [POST]
        public function actionCreateCommnetQuiz($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $comment = new QuizComment;

                    $comment->attributes = array(
                        'quiz_id'=>$quiz_id,
                        'respondent_id'=>$user['id'],
                        'text'=>trim($_POST['comment_text']),
                    );

                    if($comment->save())
                        $jsonText = CJSON::encode($this->jsonStruct());
                    else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'At the moment you can not save comment quiz. Please try again later.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/[quiz-id]/questions/?session=[user-session]
        public function actionViewStructureQuiz($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $quiz = Quiz::model()->findByPk($quiz_id);

            $jsonText = '';

            $applicationRespondentQuiz  =   null;

            if ($session) {
                $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
                $user = $sesssionID->respondent;
                $applicationRespondentQuiz = $quiz->getApplication($user['id']);
            }


            if(!$applicationRespondentQuiz) {
                $applicationRespondentQuiz = new Application;
                $groups = $quiz->groupsQuestions;
                if($groups) {
                    $applicationRespondentQuiz->attributes = $applicationRespondentQuiz->getCheckQuestion(current($groups), $quiz);
                }

                if (isset($user)) {
                    $applicationRespondentQuiz['respondent_id'] = $user['id'];
                }
                $applicationRespondentQuiz['quiz_id'] = $quiz_id;
                $applicationRespondentQuiz->save();
            }


            $jsonData['application_id'] = $applicationRespondentQuiz->id;
            $jsonData['groups'] = array();
            foreach ($quiz->groupsQuestions as $g => $group) {
                // Group Questions
                $jsonData['groups'][$g]['id'] = $group['id'];
                $jsonData['groups'][$g]['quiz_id'] = $group['quiz_id'];
                $jsonData['groups'][$g]['order'] = $group['orderby'];

                $jsonData['groups'][$g]['condition'] = array();
                if($group['condition_question_id']){
                    $jsonData['groups'][$g]['condition']['question'] = $group['condition_question_id'];
                    $jsonData['groups'][$g]['condition']['answers'] = array();
                    foreach ($group->answers as $conda => $condAnswer) {
                        $jsonData['groups'][$g]['condition']['answers'][array_search($conda, array_keys($group->answers))] = $conda;
                    }
                }
                else {
                    $jsonData['groups'][$g]['condition'] = null;
                }

                $jsonData['groups'][$g]['questions'] = array();
                foreach ($group->questions as $q => $question) {
                    // Question attributes
                    $jsonData['groups'][$g]['questions'][$q]['id'] = $question['id'];
                    $jsonData['groups'][$g]['questions'][$q]['text'] = $question['text'];
                    $jsonData['groups'][$g]['questions'][$q]['type'] = $question['type'];
                    $jsonData['groups'][$g]['questions'][$q]['order'] = $question['orderby'];
                    $jsonData['groups'][$g]['questions'][$q]['scaled_size'] = $question['scaled_size'];
                    $jsonData['groups'][$g]['questions'][$q]['is_not_required'] = $question['is_not_required'];

                    // Question pictures
                    $jsonData['groups'][$g]['questions'][$q]['photos'] = array();
                    foreach ($question->pictures as $p => $picture){
                        $jsonData['groups'][$g]['questions'][$q]['photos'][$p]['id'] = $picture['id'];
                        $jsonData['groups'][$g]['questions'][$q]['photos'][$p]['link'] = Yii::app()->request->hostInfo.QuestionMedia::getPath().$picture['link'];
                    }
                    if(!$jsonData['groups'][$g]['questions'][$q]['photos']) $jsonData['groups'][$g]['questions'][$q]['photos'] = null;

                    // Question answers
                    $jsonData['groups'][$g]['questions'][$q]['answers'] = array();
                    foreach ($question->answers as $a => $answer){
                        $jsonData['groups'][$g]['questions'][$q]['answers'][array_search($a, array_keys($question->answers))]['id'] = $answer['id'];
                        $jsonData['groups'][$g]['questions'][$q]['answers'][array_search($a, array_keys($question->answers))]['text'] = $answer['text'];
                        $jsonData['groups'][$g]['questions'][$q]['answers'][array_search($a, array_keys($question->answers))]['order'] = $answer['orderby'];
                    }
                    if(!$jsonData['groups'][$g]['questions'][$q]['answers']) {
                        $jsonData['groups'][$g]['questions'][$q]['answers'] = null;
                    }

                    // Respondent answers on question
                    $jsonData['groups'][$g]['questions'][$q]['answer_value'] = array();
                    $respondentAnswers = $applicationRespondentQuiz->answers(array('condition'=>'question_id="'.$question['id'].'"'));
                    if($respondentAnswers){
                        foreach ($respondentAnswers as $appl=>$applAnswer){
                            $jsonData['groups'][$g]['questions'][$q]['answer_value'][$appl]['id'] = $applAnswer['answer_id'];
                            if($question['type'] == Question::TYPE_ANSWPHOTO) {
                                $jsonData['groups'][$g]['questions'][$q]['answer_value'][$appl]['text'] = Yii::app()->request->hostInfo.Application::getPath().$applAnswer['answer_text'];
                            }
                            else {
                                $jsonData['groups'][$g]['questions'][$q]['answer_value'][$appl]['text'] = $applAnswer['answer_text'];
                            }
                        }
                    }
                    else {
                        $jsonData['groups'][$g]['questions'][$q]['answer_value'] = null;
                    }
                }

                /*if($group['id'] == $applicationRespondentQuiz['check_question_group_id']){
                    $questionCh = $applicationRespondentQuiz->checkQuestion;
                    if($questionCh){
                        $chooseCheckQuestion['id'] = $questionCh['id'];
                        $chooseCheckQuestion['text'] = $questionCh['text'];
                        $chooseCheckQuestion['type'] = Question::TYPE_CLOSE;
                        $chooseCheckQuestion['order'] = $applicationRespondentQuiz['check_question_order'] + 1;
                        $chooseCheckQuestion['scaled_size'] = 0;
                        $chooseCheckQuestion['photos'] = array();
                        $chooseCheckQuestion['answers'] = array();
                        foreach ($questionCh->answers as $a => $answer){
                            $chooseCheckQuestion['answers'][$a]['id'] = $answer['id'];
                            $chooseCheckQuestion['answers'][$a]['text'] = $answer['text'];
                            $chooseCheckQuestion['answers'][$a]['order'] = $a + 1;
                        }
                        $chooseCheckQuestion['answer_value'] = array();
                        if($applicationRespondentQuiz->check_answers_id) {
                            array_push($chooseCheckQuestion['answer_value'], array('id'=>$applicationRespondentQuiz->check_answers_id));
                        }
                        else {
                            $chooseCheckQuestion['answer_value'] = null;
                        }
                        $chooseCheckQuestion['photos'] = null;
                        array_splice( $jsonData['groups'][$g]['questions'], $applicationRespondentQuiz['check_question_order'], 0, array($chooseCheckQuestion) );
                    }
                }*/
            }

            # past check-question in end quiz
            $questionCh = $applicationRespondentQuiz->checkQuestion;
            if($questionCh){
                $chooseCheckQuestion['id'] = $questionCh['id'];
                $chooseCheckQuestion['text'] = $questionCh['text'];
                $chooseCheckQuestion['type'] = Question::TYPE_CLOSE;
                $chooseCheckQuestion['order'] = $applicationRespondentQuiz['check_question_order'] + 1;
                $chooseCheckQuestion['scaled_size'] = 0;
                $chooseCheckQuestion['photos'] = array();
                $chooseCheckQuestion['answers'] = array();
                foreach ($questionCh->answers as $a => $answer){
                    $chooseCheckQuestion['answers'][$a]['id'] = $answer['id'];
                    $chooseCheckQuestion['answers'][$a]['text'] = $answer['text'];
                    $chooseCheckQuestion['answers'][$a]['order'] = $a + 1;
                }
                $chooseCheckQuestion['answer_value'] = array();
                if($applicationRespondentQuiz->check_answers_id) {
                    array_push($chooseCheckQuestion['answer_value'], array('id'=>$applicationRespondentQuiz->check_answers_id));
                }
                else {
                    $chooseCheckQuestion['answer_value'] = null;
                }
                $chooseCheckQuestion['photos'] = null;
                array_splice( $jsonData['groups'][$g]['questions'], $applicationRespondentQuiz['check_question_order'], 0, array($chooseCheckQuestion) );
            }

            $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $jsonData));


            #    $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/[quiz-id]/stats/?session=[user-session]
        public function actionViewStatisticsQuiz($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $quiz = Quiz::model()->findByPk($quiz_id);

                    $stats['questions'] = array();
                    foreach ($quiz->groupsQuestions as $g => $group) {
                        foreach ($group->questions as $q => $question) {
                            if($question['type'] != Question::TYPE_OPEN && $question['type'] != Question::TYPE_ANSWPHOTO) {
                                $statsQuestion = array();
                                // Question attributes
                                $statsQuestion['id'] = $question['id'];
                                $statsQuestion['quiz_id'] = $quiz_id;
                                $statsQuestion['text'] = $question['text'];
                                $statsQuestion['order'] = $question['orderby'];
                                if($question['type'] == Question::TYPE_CLOSE_MULTISEL)
                                    $statsQuestion['type'] = Question::API_TYPE_MULTIPLE;
                                elseif($question['type'] == Question::TYPE_SCALE_SCORE)
                                    $statsQuestion['type'] = Question::API_TYPE_KPI;
                                else
                                    $statsQuestion['type'] = Question::API_TYPE_SINGLE;

                                $statsQuestion['answers'] = array();
                                foreach($question->getStatsAnswers() as $sID=>$statsAnsw){
                                    if($question['type'] == Question::TYPE_SCALE_SCORE){
                                        $statsQuestion['kpi'] = $statsAnsw;
                                        $statsQuestion['answers'] = null;
                                    }else{
                                        $statsQuestion['kpi'] = null;
                                        $answer = Answer::model()->find(array(
                                            'select'=>'text',
                                            'condition'=>'id=:answID',
                                            'params'=>array(':answID'=>$sID),
                                        ));
                                        array_push($statsQuestion['answers'], array('id'=>$sID, 'text'=>($answer ? $answer['text'] : 'other'), 'count'=>$statsAnsw));
                                    }
                                }
                                array_push($stats['questions'], $statsQuestion);
                            }
                        }
                    }

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $stats));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/quiz/[quiz-id]/photos/[question-id]/?session=[user-session]
        public function actionCreatePhotoApplication($id, $question, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $question_id = htmlspecialchars(trim($question));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            #if($sesssionID){
            # $user = $sesssionID->respondent;
            #if($user){
            
            $user = new Respondent;
            $question = Question::model()->find(array(
                'select'=>'type',
                'condition'=>'id=:questionID',
                'params'=>array(':questionID'=>$question_id),
            ));

            if($question['type'] == Question::TYPE_ANSWPHOTO){
                $name = Application::getPath().$this->updatePhoto($user, 'question_photo', 'image');
                if($user->image->saveAs(Yii::getPathOfAlias('webroot').$name)){
                    $image = Yii::app()->image->load(Yii::getPathOfAlias('webroot').$name);
                    list($width, $height, $type, $attr) = getimagesize(Yii::getPathOfAlias('webroot').$name);
                    if($width > 1000){
                        $image->resize(1000, 1000);
                        $image->save();
                    }
                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', array('url'=>Yii::app()->request->hostInfo.$name)));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(4, Yii::t('app', 'No save image.')));
            } else
                $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'Question should be a type of Answer photos.')));
            
            #  }else
            #      $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            # }else
            #    $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/quiz/[quiz-id]/application/?q[[question-id]]=[question-answer]&session=[user-session]
        public function actionUpdateApplication($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $application_id = htmlspecialchars(trim($_POST['application_id']));
            $omi_aud_id = htmlspecialchars(trim($_POST['omi_aud_id']));
            $quiz = Quiz::model()->findByPk($quiz_id);

            // по сессии определяем пользователя, если он есть, конечно
            $user = null;
            if ($session) {
                $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
                $user = $sesssionID->respondent;
            }

            // анонимный пользователь, работаем с идентификатором анкеты
            if ($application_id) {
                $applicationRespondentQuiz  =   Application::model()->findByPk($application_id);
                $applicationRespondentQuiz->omi_aud_id = $omi_aud_id;

                // также есть сессия - пробуем присвоить респондента
                if ($user) {
                    $applicationRespondentQuiz->respondent_id = $user['id'];
                }
            }
            // если передана сессия, то работае по ней
            else {
                $applicationRespondentQuiz = $quiz->getApplication($user['id']);      
            }

            $jsonText = '';
                    
            if($applicationRespondentQuiz['state'] == Application::STATE_DONE || $applicationRespondentQuiz['state'] == Application::STATE_CLOSE){
                $jsonText = CJSON::encode($this->jsonStruct(4, Yii::t('app', 'Application has been filled.')));
            }
            else {
                $answersQuestions = CJSON::decode($_POST['app_blank']);
                $is_save = true;

                foreach($answersQuestions as $q => $question){
                    if($question) {
                        $applAnswer = array();
                        if(!DictCheckQuestions::model()->count('id=:question', array(':question'=>$question['question_id']))){
                            $applAnswer['question_id'] = $question['question_id'];
                            ApplicationAnswer::model()->deleteAll('application_id=:application AND question_id=:question', array(':application'=>$applicationRespondentQuiz['id'],':question'=>$question['question_id']));
                            foreach($question['answer_value'] as $a => $answer){
                                if($answer && $answer != 'skip_answer_button_was_pressed') {
                                    if($answer['id']){
                                        $applAnswer['answer_id'] = $answer['id'];
                                    }
                                    elseif($answer['text']){
                                        $applAnswer['answer_text'] = $answer['text'];
                                    }

                                    $modelapplAnswer = new ApplicationAnswer;
                                    $applAnswer['application_id'] = $applicationRespondentQuiz['id'];
                                    $modelapplAnswer->attributes = $applAnswer;
                                    if(!$modelapplAnswer->save()){
                                        $is_save = false;
                                        break;
                                    }
                                }
                            }
                        }
                        else {
                            $applicationRespondentQuiz['check_answers_id'] = $question['answer_value'][0]['id'];
                        }

                        if(!$is_save) break;
                    }
                }

                if($is_save) {
                    $applicationRespondentQuiz['date_filled'] = date('Y-m-d H:i:s');
                    $applicationRespondentQuiz['state'] = Application::STATE_DONE;
                    $applicationRespondentQuiz->save();
                    $jsonText = CJSON::encode($this->jsonStruct());
                }
                else {
                    $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'At the moment you can not send the questionnaire. Please try again later.')));
                }
            }
 
            $this->_sendResponse(200, $jsonText);
        }

        # GET /api/v1/quiz/[quiz-id]/application/comments/?session=[user-session]
        # list of appeals to application in quiz [GET]
        public function actionListCommentsApplication($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $quiz = Quiz::model()->findByPk($quiz_id);
                    $applicationRespondentQuiz = $quiz->getApplication($user['id']);
                    $result['comments'] = array();
                    if($applicationRespondentQuiz && $applicationRespondentQuiz->commentsRejectAndAppealCount > 0)
                        foreach($applicationRespondentQuiz->commentsRejectAndAppeal as $c => $comment){
                            $elemComment = array(
                                "id" => $comment['id'],
                                "quiz_id" => $id,
                                "application" => $comment['application_id'],
                                "text" => $comment['text'],
                                "datetime" => $comment['date_created'],
                                "state" => Application::itemAlias('StatusApplication', $comment['state']),
                                "role" => ApplicationComment::itemAlias('RoleSender', $comment['role'])
                            );

                            array_push($result['comments'], $elemComment);
                        }

                    $jsonText = CJSON::encode($this->jsonStruct(0, 'OK', $result));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        # POST /api/v1/quiz/[quiz-id]/application/comments/?text=[comment-text]&session=[user-session]
        public function actionCreateAppealApplication($id, $session)
        {
            $quiz_id = htmlspecialchars(trim($id));
            $sesssionID = $this->loadSession(htmlspecialchars(trim($session)));
            $jsonText = '';

            if($sesssionID){
                $user = $sesssionID->respondent;
                if($user){
                    $quiz = Quiz::model()->findByPk($quiz_id);
                    $applicationRespondentQuiz = $quiz->getApplication($user['id']);

                    $comment = new ApplicationComment;

                    $comment->attributes = array(
                        'application_id'=>$applicationRespondentQuiz['id'],
                        'state'=>Application::STATE_APPEAL,
                        'role'=>ApplicationComment::ROLE_RESPONDENT,
                        'text'=>trim($_POST['appeal_text'])
                    );

                    if($comment->save())
                        $jsonText = CJSON::encode($this->jsonStruct());
                    else
                        $jsonText = CJSON::encode($this->jsonStruct(3, Yii::t('app', 'At the moment you can not submit an appeal. Please try again later.')));
                }else
                    $jsonText = CJSON::encode($this->jsonStruct(2, Yii::app()->getModule('respondent')->t('Respondent not found.')));
            }else
                $jsonText = CJSON::encode($this->jsonStruct(1, Yii::app()->getModule('respondent')->t('Respondent not authorized.')));

            $this->_sendResponse(200, $jsonText);
        }

        /**
	 * Returns the data model session based on the primary key given in the GET variable.
	 * @param integer the ID of the model session to be loaded
	 */
	public function loadSession($session_id)
	{
		$model = Session::model()->findByPk($session_id);
		return $model;
	}

        /**
	 * Returns the data model session based on the primary key given in the GET variable.
	 * @param integer the ID of the model session to be loaded
	 */
	public function pointEndLifetime($number) { return date('Y-m-d H:i:s', mktime(date("H") + $number, date("i"), date("s"), date("m"), date("d"), date("Y"))); }

        /**
	 * Returns the data model session based on the primary key given in the GET variable.
	 * @param integer the ID of the model session to be loaded
	 */
	public function textEMail($view, $messenge) { return array('body'=>array('view'=>$view,'content'=>$messenge)); }

        # Action Error
        public function actionError()
        {
            $jsonText = CJSON::encode($this->jsonStruct(1, Yii::t('app', 'An error has occurred. Please contact your administrator.')));
            $this->_sendResponse(200, Utils::json_encode_cyr($jsonText));
        }

        # JSON structure
        private function jsonStruct($code = 0, $message = 'OK', $body = null)
        {
            return array('status_code' => $code, 'status_message' => $message, 'data' => $body);
        }

       /*--------------
        * Upload and crunch an image
        ----------------*/
        public function updatePhoto($model, $namefile, $attr) {
           $myfile = CUploadedFile::getInstanceByName($namefile);
           $model->$attr = $myfile;
           if (is_object($myfile) && get_class($myfile)==='CUploadedFile')
                return dechex(rand()%999999999) . '.' . $model->$attr->getExtensionName();  // Filename: Random name and extension name
        }

        #Update table Captcha if isset respondent
        private function verificationCaptcha($respondent_id = null){
            $img_code = htmlspecialchars(trim($_POST['code_img']));
            $captcha = htmlspecialchars(trim($_POST['verify_code']));
            $record = Yii::app()->db->createCommand()
                    ->select('id, code')
                    ->from('{{captcha}}')
                    ->where('img_name=:code', array(':code'=>$img_code))
                    ->queryRow();
            if($record){
                if($respondent_id)
                    Yii::app()->db->createCommand()->update('{{captcha}}', array(
                            'respondent_id'=>$respondent_id,
                        ), 'id=:id', array(':id'=>$record['id']));

                if($record['code'] == $captcha)
                    return true;
            }
            return false;
        }

        # Sending the Response
        private function _sendResponse($status = 200, $body = '', $content_type = 'text/html; charset=utf-8') {
            // set the status
            if ($status != "200") {
				$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
            	header($status_header);
            }

			#$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
			#header($status_header, true, $status);


            // and the content type
            header('Content-type: ' . $content_type);

            // pages with body are easy
            if($body != '')
            {
                // send the body
                echo $body;
            }
            // we need to create the body if none is passed
            else
            {
                // create some body messages
                $message = '';

                // this is purely optional, but makes the pages a little nicer to read
                // for your users.  Since you won't likely send a lot of different status codes,
                // this also shouldn't be too ponderous to maintain
                switch($status)
                {
                    case 401:
                        $message = 'You must be authorized to view this page.';
                        break;
                    case 404:
                        $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                        break;
                    case 500:
                        $message = 'The server encountered an error processing your request.';
                        break;
                    case 501:
                        $message = 'The requested method is not implemented.';
                        break;
                }

                // servers don't always have a signature turned on
                // (this is an apache directive "ServerSignature On")
                $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

                // this should be templated in a real-world solution
                $body = '
                <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                </head>
                <body>
                    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                    <p>' . $message . '</p>
                    <hr />
                    <address>' . $signature . '</address>
                </body>
                </html>';

                echo $body;
            }
            Yii::app()->end();
        }

        # Getting the Status Codes
        private function _getStatusCodeMessage($status)
        {
            // these could be stored in a .ini file and loaded
            // via parse_ini_file()... however, this will suffice
            // for an example
            $codes = Array(
                200 => 'OK',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
            );
            return (isset($codes[$status])) ? $codes[$status] : '';
        }
}