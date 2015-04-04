<?php

/**
 * This is the model class for table "{{quiz}}".
 *
 * The followings are the available columns in table '{{quiz}}':
 * @property integer $quiz_id
 * @property string $title
 * @property integer $client_id
 * @property integer $anonymous_client
 * @property integer $manager_id
 * @property string $fill_time
 * @property string $description
 * @property string $type
 * @property double $money
 * @property double $karma
 * @property string $date_created
 * @property string $date_start
 * @property string $date_stop
 * @property string $deadline
 * @property string $state
 * @property integer $needs_confirmation
 * @property integer $skip_start_page
 *
 * The followings are the available model relations:
 * @property Users $manager
 * @property Clients $client
 */
class Quiz extends CActiveRecord {
    const DELETED = '1';
    const NO_DELETED = '0';
    const TYPE_GENERAL = 'quiz';
	const TYPE_MISSION = 'mission';

	const STATE_EDIT = 'edit';
	const STATE_MODERATION = 'moderation';
	const STATE_WORK = 'work';
	const STATE_FILL = 'fill';
	const STATE_REFUSE = 'refuse';

        const KIND_FREE = 'free';
	const KIND_PAID = 'paid';
	const KIND_GROUP = 'group';

        public $isSendMessenge;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Quiz the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{quiz}}';
	}

        /**
	 * This method is invoked before saving a record (after validation, if any).
	 * The default implementation raises the {@link onBeforeSave} event.
	 * You may override this method to do any preparation work for record saving.
	 * Use {@link isNewRecord} to determine whether the saving is
	 * for inserting or updating record.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the saving should be executed. Defaults to true.
	 */
        protected function beforeSave()
        {
            if(parent::beforeSave())
            {
                if($this->isNewRecord) {
                    $diction = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
                    $chars = strlen($diction);
                    $hash = '';

                    for ($i = 0; $i < 15; $i++) {
                        $hash .= substr($diction, rand(1, $chars) - 1, 1);
                    }
                    $this->hash = $hash;

                    $this->manager_id = Yii::app()->user->id;
                    $this->date_created = date('Y-m-d H:i:s');
                } else
                    $this->date_created=preg_replace("/(\d\d).(\d\d).(\d\d\d\d) (\d\d):(\d\d):(\d\d)/", "\\3-\\2-\\1 \\4:\\5:\\6", $this->date_created);

                if($this->state == self::STATE_WORK || $this->state == self::STATE_FILL){
                    if(!$this->date_start) $this->date_start = date('Y-m-d H:i:s');
                    if($this->state == self::STATE_FILL && !$this->date_stop) $this->date_stop = date('Y-m-d H:i:s');
                }
                if($this->deadline)
                    $this->deadline = preg_replace("/(\d\d).(\d\d).(\d\d\d\d)/", "\\3-\\2-\\1 00:00:00", $this->deadline);

                return true;
            }
            else
                return false;
        }

        protected function afterSave()
        {
           /* if((!$this->_oldState || ($this->_oldState && $this->_oldState != $this->state)) && $this->state == self::STATE_WORK)
                $this->sendPushNotification();*/

            if($this->_oldState != $this->state){
                if($this->state == self::STATE_FILL){
                    foreach ($this->applications(array('condition'=>'state != "'.Application::STATE_CLOSE.'"')) as $a => $appl){
                        $messenge = '';
                        if($appl->state == Application::STATE_TODO){
                            $appl->state = Application::STATE_CLOSE;
                            $appl->save();
                            $messenge = Yii::t('app', 'Your application for this quiz was not filled. Therefore, it was closed. Remuneration provided for the passage of this quiz, your balance will not be credited.');
                        }elseif($appl->state == Application::STATE_APPEAL || $appl->state == Application::STATE_DONE)
                            $messenge = Yii::t('app', 'Your application for the quiz is not closed. At the moment she is on the check with the manager.');
                        elseif($appl->state == Application::STATE_REJECT)
                            $messenge = Yii::t('app', 'Your application for the quiz is not closed. Fix these observations manager.');

                        if($messenge){
                            $messenge = Yii::t('app', 'The quiz {{$quiz}} has been closed.', array('{{$quiz}}'=>$this->title)).' '.$messenge;
                            Utils::send_sms($appl->Respondent->phone_number, $messenge);
                        }
                    }
                }
                // this notification sends in lauch action
                #elseif($this->state == self::STATE_WORK && $this->isSendMessenge) {
                elseif($this->state == self::STATE_WORK) {
                    $this->sendPushNotification();
                    /*$respondentsPhones = array();

                    foreach ($this->audience as $t => $data) {
                        foreach ($data->getRespondents() as $i => $respondent) {
                            $respondentsPhones[$respondent['id']] = $respondent['phone_number'];
                        }
                    }

                    $messenge = Yii::t('app', 'Available new quiz {{$quiz}}.', array('{{$quiz}}'=>$this->title));
                    Utils::send_sms($respondentsPhones, $messenge);*/
                }

				// refuse this quiz
                if ($this->_oldState == self::STATE_MODERATION) {
                    if ($this->state == self::STATE_WORK) {
                        Mailtpl::send("moderation_success", Yii::app()->user->email, array("{title}"=>$this->title, "{refuse}"=>$this->refuse));
 
                    }

                    if ($this->state == self::STATE_REFUSE) {
                        Mailtpl::send("moderation_fail", Yii::app()->user->email, array("{title}"=>$this->title, "{refuse}"=>$this->refuse));
                    }
                }
                if($this->state == self::STATE_REFUSE) {
                	$email = Yii::app()->user->user(Quiz::model()->findByPk($this->quiz_id)->manager_id)->email;
 					UserModule::sendMail($email, Yii::t('app', 'Your quiz is refuse on moderation'), Yii::t('app', 'Your quiz is refuse on moderation by {refuse}', array('{refuse}'=>$this->refuse)));
                }
            }
            parent::afterSave();
        }

        private $_oldState;
        /**
	 * This method is invoked after find a record.
	 */
        protected function afterFind()
        {
            parent::afterFind();
            $this->date_created=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/", "\\3.\\2.\\1 \\4:\\5:\\6", $this->date_created);
            if($this->deadline)
                $this->deadline=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/", "\\3.\\2.\\1", $this->deadline);
            $this->_oldState = $this->state;
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, client_id, description', 'required'),
            array('client_id, anonymous_client, manager_id, needs_confirmation, archive, skip_start_page', 'numerical', 'integerOnly'=>true),
			array('money', 'numerical'),
			array('karma', 'numerical'),
			array('title', 'length', 'max'=>150),
			array('fill_time', 'length', 'max'=>255),
			array('type', 'length', 'max'=>7),
			array('state', 'length', 'max'=>12),
                        array('description, title, fill_time, money, karma','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			array('description, date_created, date_start, date_stop, refuse', 'safe'),
                        array('deadline', 'date', 'format'=>'dd.MM.yyyy'),
                        array('deadline', 'default', 'value'=>null),
                        array('money', 'default', 'value'=>0),
                        array('karma', 'default', 'value'=>0),

                        array('is_mailsender, mail_template', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('quiz_id, title, client_id, anonymous_client, manager_id, fill_time, description, type, money, karma, date_created, date_start, date_stop, deadline, state, needs_confirmation, archive, refuse, hash, is_deleted, is_mailsender, mail_template', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'manager' => array(self::BELONGS_TO, 'User', 'manager_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'audience' => array(self::HAS_MANY, 'TargetAudience', 'quiz_id'),
			'groupsQuestions' => array(self::HAS_MANY, 'GroupQuestions', 'quiz_id','order'=>'groupsQuestions.orderby'),
			'comments' => array(self::HAS_MANY, 'QuizComment', 'quiz_id','order'=>'comments.date_created DESC'),
            'applications' => array(self::HAS_MANY, 'Application', 'quiz_id','index'=>'id'),
            'applications_closed' => array(self::HAS_MANY, 'Application', 'quiz_id','index'=>'id', 'condition'=>'state LIKE "'.Application::STATE_CLOSE.'"'),
			'countApplications' => array(self::STAT, 'Application', 'quiz_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'quiz_id' => 'Quiz',
			'title' => Yii::t('app', 'Title'),
			'client_id' => Yii::t('app', 'Client Name'),
			'anonymous_client' => Yii::t('app', 'Anonymous Client'),
			'manager_id' => Yii::t('app', 'Manager'),
			'fill_time' => Yii::t('app', 'Fill Time'),
			'description' => Yii::t('app', 'Description'),
			'refuse' => Yii::t('app', 'Refuse quiz'),
			'type' => Yii::t('app', 'Type'),
			'money' => Yii::t('app', 'Money'),
			'karma' => Yii::t('app', 'Karma'),
			'date_created' => Yii::t('app', 'Date Created'),
			'date_start' => Yii::t('app', 'Date Start'),
			'date_stop' => Yii::t('app', 'Date Stop'),
			'deadline' => Yii::t('app', 'Deadline'),
			'state' => Yii::t('app', 'State'),
			'needs_confirmation' => Yii::t('app', 'Needs Confirmation'),
			'archive' => Yii::t('app', 'Archived'),
			'isSendMessenge' => Yii::t('app', 'Send a notice of the survey participants'),
            'skip_start_page' => 'Опрос без стартовой страницы',
		);
	}


	/*
		This method is invoked before validation a record.
	*/
	protected function beforeValidate() {
		if(parent::beforeValidate()){
			if($this->isNewRecord) {
				// check num quizs in user tariff
				if (!User::model()->findByPk(Yii::app()->user->id)->subfor) {
					$count = current(Yii::app()->db->createCommand()
					->select('count(id)')
					->from('tbl_quiz quiz')
					->join('tbl_users user', 'user.id = quiz.manager_id')
					->where(
						'manager_id=:id AND date_created BETWEEN LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)',
						array(':id'=>Yii::app()->user->id)
					)
					->queryRow());

					$license	=	User::model()->findByPk(Yii::app()->user->id)->license[0];
					$max	=	$license->limits->limit_quizs;

					if ($count >= $max && $max !== "0" || $license->active != "yes") {
						$this->addError("", Yii::t('app', 'The maximum number of quizs', array("{max}" => $max)));
						return false;
					}
				}
			}

			if ($this->money) {
            	if ($this->money < Yii::app()->params['minCostRespondent']) {
            		$this->addError("", Yii::t('app', 'Minimum cost of respondent is'));
					return false;
            	}
			}

			return true;
		}
		else {
			return false;
		}
	}


        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'QuizType' => array(
                    self::TYPE_GENERAL => Yii::t('app', 'General Quiz'),
                    self::TYPE_MISSION => Yii::t('app', 'Mission'),
                ),
                'QuizStateNotFill' => array(
                    self::STATE_EDIT => Yii::t('app', 'Edit State'),
                    self::STATE_WORK => Yii::t('app', 'Work State'),
                ),
                'QuizStateWork' => array(
                    self::STATE_WORK => Yii::t('app', 'Work State'),
                    self::STATE_FILL => Yii::t('app', 'Fill State'),
                ),
                'QuizStateModeration' => array(
                    self::STATE_EDIT => Yii::t('app', 'Edit State'),
                    self::STATE_MODERATION => Yii::t('app', 'Moderation State'),
                ),
                'QuizState' => array(
                    self::STATE_EDIT => Yii::t('app', 'Edit State'),
                    self::STATE_MODERATION => Yii::t('app', 'Moderation State'),
                    self::STATE_WORK => Yii::t('app', 'Work State'),
                    self::STATE_FILL => Yii::t('app', 'Fill State'),
                    self::STATE_REFUSE => Yii::t('app', 'Refuse State'),
                ),
            );

            if (isset($code))
                return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
            else
                return isset($_items[$type]) ? $_items[$type] : false;
	}

        /**
         * Количество вопросов опроса
         */
        public function countQuestions() {
            $questionsArr = array();

            foreach ($this->groupsQuestions as $g => $group) {
                foreach ($group->questions as $q => $question) {
                    $questionsArr[$question['id']] = $question;
                }
            }

            return count($questionsArr);
        }

        /**
         * Количество респондентов, для которых доступен опрос
         */
        public function countRespondets() {
            $respondentsArr = array();

            foreach ($this->audience as $t => $data) {
                foreach ($data->getRespondents() as $i => $respondent) {
                    $respondentsArr[$respondent['id']] = 1;
                }
            }

            return count($respondentsArr);
        }

        /**
         * Количество респондентов требующиеся для опроса
         */
        public function totalRespondets() {
            $respondentsArr = -1;

            foreach ($this->audience as $t => $data) {
                if($data['count_limit']){
                    if($respondentsArr < 0) $respondentsArr = 0;
                    $respondentsArr += $data['count_limit'];
                }
            }

            return $respondentsArr >=0 ? $respondentsArr : null;
        }

        /**
         * Возвращает анкету пользователя для данного опроса
         */
        public function getApplication($respondent) {
            return current($this->applications(array('condition'=>'respondent_id='.$respondent)));
        }

        /**
         * Отправляем Push-уведомление о новом опросе
         */
        public function sendPushNotification() {
            $ios = array();
            $android = array();

            $money = $this->money;
            if (!$this->money) {
                $money = Yii::t('app', 'Free cost quiz');
            }
 
            $time = $this->fill_time;
            if (!$this->fill_time) {
                $time = Yii::t('app', 'Non time quiz');
            }

            $client = $this->client->name;
            if ($this->anonymous_client) {
                $client = Yii::t('app', 'Hide company quiz');
            }

            $template = self::bb($this->mail_template);
            foreach ($this->audience as $t => $data) {
                foreach ($data->getRespondents() as $i => $respondent) {
                    if ($respondent->sessions['device_token']) {
                        if ($respondent->sessions['device_type'] == "android") {
                            $android[] = $respondent->sessions['device_token'];
                        }
                        else {
                            $ios[] = $respondent->sessions['device_token'];
                        }
                    }

                    if ($this->is_mailsender) {
                        $option = str_replace(array("[FirstName]", "[LastName]"), array($respondent->first_name, $respondent->last_name), $template);
                        Mailtpl::send("new_quiz", $respondent->email_actual, array("{title}"=>$this->title, "{description}"=>$this->description, "{money}"=>$money, "{time}"=>$time, "{client}"=>$client, "{hash}"=>$this->hash, "{option}"=>$option));
                    }
                }
            }

            $messenge = Yii::t('app', 'New quiz').' '.$this->title;
            Yii::app()->getModule('respondent')->sendNotifications($messenge, $ios);
            
            $gcm = Yii::app()->gcm;
            $gcm->send($android, $messenge);

        }


        	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

                if(!Yii::app()->getModule('user')->isAdmin()){
                    $clients = Yii::app()->user->getClients(Yii::app()->user->id);
                    $criteria->addInCondition('client_id', array_keys($clients));
                }

		//$criteria->compare('quiz_id',$this->quiz_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('client_id',$this->client_id);
		//$criteria->compare('anonymous_client',$this->anonymous_client);
		//$criteria->compare('manager_id',$this->manager_id);
		//$criteria->compare('fill_time',$this->fill_time,true);
		//$criteria->compare('description',$this->description,true);
		$criteria->compare('type',$this->type,true);
		//$criteria->compare('money',$this->money);
		//$criteria->compare('karma',$this->karma);
		$criteria->compare('DATE(date_created)', Utils::pack_date($this->date_created),true);
		//$criteria->compare('date_start',$this->date_start,true);
		//$criteria->compare('date_stop',$this->date_stop,true);
        $criteria->compare('DATE(deadline)', Utils::pack_date($this->deadline),true);
		$criteria->compare('state',$this->state,true);
		//$criteria->compare('needs_confirmation',$this->needs_confirmation);
		if($this->archive)
                $criteria->compare('archive',$this->archive,true);
            else
                $criteria->addCondition('archive IS NULL OR archive = 0');

        $criteria->order = 'date_created DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /*
         *
         */
        public function getIdsGroupsInAudiences(){
            $criteria=new CDbCriteria;
            $criteria->with = array('audience');
            $criteria->addCondition('audience.quiz_id=:quiz');
            $criteria->params = array(':quiz'=>$this->quiz_id);
            $criteria->select = 'id';
            $criteria->index = 'id';
            $criteria->distinct = true;
            $groups = GroupRespondents::model()->findAll($criteria);
            return array_keys($groups);
        }

    public static function bb($content) {
        $content = str_ireplace(array('[b]', '[/b]'), array('<b>', '</b>'), $content);
        $content = str_ireplace(array('[i]', '[/i]'), array('<i>', '</i>'), $content);
        $content = str_ireplace(array('[u]', '[/u]'), array('<u>', '</u>'), $content);

        $content = str_ireplace('[left]', '<div style="text-align:left;">', $content);
        $content = str_ireplace('[center]', '<div style="text-align:center;">', $content);
        $content = str_ireplace('[right]', '<div style="text-align:right;">', $content);
        $content = str_ireplace(array('[/left]', '[/center]', '[/right]'), '</div>', $content);

        preg_match_all("#\[link=([^\]]+)\]([^\]]+)\[/link\]#i", $content, $array);
        for ($i = 0, $count = count($array[1]); $i < $count; $i++) {
            $content = str_ireplace('[link='.$array[1][$i].']'.$array[2][$i].'[/link]', '<a href="'.$array[1][$i].'">'.$array[2][$i].'</a>', $content);
        }

        return str_ireplace("\n", '<br>', $content);
    }
}