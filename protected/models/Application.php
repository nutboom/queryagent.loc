<?php

/**
 * This is the model class for table "{{applications}}".
 *
 * The followings are the available columns in table '{{applications}}':
 * @property integer $id
 * @property integer $respondent_id
 * @property integer $quiz_id
 * @property string $state
 * @property string $date_created
 * @property string $date_changed
 * @property string $date_filled
 * @property string $date_closed
 * @property integer $check_question_id
 * @property integer $check_question_order
 * @property integer $check_question_group_id
 *
 * The followings are the available model relations:
 * @property ApplicationAnswers[] $applicationAnswers
 * @property ApplicationComments[] $applicationComments
 * @property GroupQuestions $checkQuestionGroup
 * @property DictCheckQuestions $checkQuestion
 * @property Quiz $quiz
 * @property Respondents $respondent
 */
class Application extends CActiveRecord
{
        const STATE_AVAILABLE = 'available';
        const STATE_TODO = 'todo';
	const STATE_DONE = 'done';
	const STATE_CLOSE = 'close';
	const STATE_REJECT = 'reject';
	const STATE_APPEAL = 'appeal';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Application the static model class
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
		return '{{applications}}';
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
                    $this->state = self::STATE_TODO;
                    $this->date_created = date('Y-m-d H:i:s');
                }else{
                    if($this->state == self::STATE_DONE){
                        if(!$this->isNewRecord && $this->check_answers_id)
                            $this->verificationTestQuestions();
                        if(!$this->quiz['needs_confirmation'] || ($this->check_answers_id && !$this->checkAnswer['is_true']))
                            $this->state = self::STATE_CLOSE;
                    }

                    if($this->_oldState != $this->state && $this->state == Application::STATE_CLOSE){
                        $this->date_closed = date('Y-m-d H:i:s');
                    }
                }

                return true;
            }
            else
                return false;
        }

        protected function afterSave()
        {
            if(!$this->isNewRecord) {
                if($this->_oldState != $this->state && $this->state == Application::STATE_CLOSE && ($this->is_true_answer && $this->is_true_answer > 0) && $this->respondent_id)
                    $this->Respondent->addProfit($this->quiz_id);

                if(($this->_oldState != Application::STATE_TODO && $this->_oldState != Application::STATE_CLOSE && $this->state == Application::STATE_CLOSE) ||
                        (($this->_oldState == Application::STATE_DONE || $this->_oldState == Application::STATE_APPEAL) && $this->state == Application::STATE_REJECT))
                    $this->sendPushNotification ();
            }
            parent::afterSave();
        }

        private $_oldState;

        protected function afterFind()
        {
            parent::afterFind();
            $this->_oldState = $this->state;
            if(!$this->isNewRecord && $this->is_true_answer == NULL)
                $this->is_true_answer = -1;
        }

        protected function afterDelete()
        {
            parent::afterDelete();
            ApplicationComment::model()->deleteAll('application_id='.$this->id);
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('respondent_id, quiz_id, check_question_order, check_question_group_id, is_true_answer, is_appeal', 'numerical', 'integerOnly'=>true),
			array('state', 'length', 'max'=>6),
			array('check_question_id, check_answers_id', 'length', 'max'=>50),
			array('date_created, date_changed, date_filled, date_closed', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, respondent_id, quiz_id, state, date_created, date_changed, date_filled, date_closed, check_question_id, check_question_order, check_question_group_id, check_answers_id, is_true_answer, is_appeal', 'safe', 'on'=>'search'),
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
			'answers' => array(self::HAS_MANY, 'ApplicationAnswer', 'application_id'),
			'comments' => array(self::HAS_MANY, 'ApplicationComment', 'application_id','order'=>'comments.date_created DESC'),
			'commentsRejectAndAppeal' => array(self::HAS_MANY, 'ApplicationComment', 'application_id', 'order'=>'date_created DESC'),
            'commentCount' => array(self::STAT, 'ApplicationComment', 'application_id'),
            'commentsRejectAndAppealCount' => array(self::STAT, 'ApplicationComment', 'application_id', 'condition'=>'state LIKE "'.Application::STATE_REJECT.'" OR state LIKE "'.Application::STATE_APPEAL.'"'),
			'checkQuestionGroup' => array(self::BELONGS_TO, 'GroupQuestions', 'check_question_group_id'),
			'checkQuestion' => array(self::BELONGS_TO, 'DictCheckQuestions', 'check_question_id'),
			'checkAnswer' => array(self::BELONGS_TO, 'DictCheckAnswers', 'check_answers_id'),
			'quiz' => array(self::BELONGS_TO, 'Quiz', 'quiz_id'),
			'Respondent' => array(self::BELONGS_TO, 'Respondent', 'respondent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'respondent_id' => Yii::app()->getModule('respondent')->t('Respondent'),
			'quiz_id' => Yii::t('app','Quiz'),
			'state' => Yii::t('app','State'),
			'date_created' => Yii::t('app','Date Created'),
			'date_changed' => Yii::t('app','Date Changed'),
			'date_filled' => Yii::t('app','Date Filled'),
			'date_closed' => Yii::t('app','Date Closed'),
			'check_question_id' => Yii::t('app','Check Question'),
			'check_question_order' => Yii::t('app','Check Question Order'),
			'check_question_group_id' => Yii::t('app','Check Question Group'),
			'check_answers_id' => Yii::t('app','Check Answer'),
			'is_true_answer' => Yii::t('app','Answered correctly on the test question'),
            'is_appeal' => Yii::t('app', 'Is appeal'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'StatusApplication' => array(
                    self::STATE_TODO => Yii::t('app', 'Status Todo'),
                    self::STATE_DONE => Yii::t('app', 'Status Done'),
                    self::STATE_CLOSE => Yii::t('app', 'Status Close'),
                    self::STATE_REJECT => Yii::t('app', 'Status Reject'),
                ),
                'ConfirmCustomerStatusApplication' => array(
                    self::STATE_CLOSE => Yii::t('app', 'Status Close'),
                    self::STATE_REJECT => Yii::t('app', 'Status Reject'),
                ),
                'CorrectAnswerCheckQuestionApplication' => array(
                    -1 => Yii::t('app', 'Not answer'),
                    1 => Yii::t('app', 'Correct'),
                    0 => Yii::t('app', 'Wrong'),
                ),
                'Appeal' => array(
                    1 => Yii::t('app', 'Status Appeal'),
                    0 => Yii::t('app', 'Status No Appeal'),
                ),
            );

            if (isset($code))
                return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
            else
                return isset($_items[$type]) ? $_items[$type] : false;
	}

        /**
         * Отправляем Push-уведомление о изменении статуса анкеты
         */
        public function sendPushNotification() {
            $quiz = $this->quiz;
            $token = $this->Respondent->sessions['device_token'];

            if($this->state == Application::STATE_CLOSE)
                $messenge = Yii::t('app', 'Application quiz {title} closed', array('{title}'=>$quiz['title']));
            elseif($this->state == Application::STATE_REJECT)
                $messenge = Yii::t('app', 'Application quiz {title} rejected', array('{title}'=>$quiz['title']));

            Yii::app()->getModule('respondent')->sendNotifications($messenge, $token);
        }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($quiz_id = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
                $criteria->together = true;
                $criteria->with= array('Respondent');
		//$criteria->compare('id',$this->id);
                if(intval($this->respondent_id))
                    $criteria->compare('respondent_id',$this->respondent_id, true);
                else{
                    $criteria->addSearchCondition('Respondent.first_name',$this->respondent_id);
                    $criteria->addSearchCondition('Respondent.last_name',$this->respondent_id,true,'OR');
                }
                if($quiz_id)
                    $criteria->compare('quiz_id', $quiz_id);
                else
                    $criteria->compare('quiz_id', $this->quiz_id, true);
		$criteria->compare('state',$this->state,true);
        $criteria->compare('is_appeal',$this->is_appeal,true);
		$criteria->compare('DATE(date_created)', Utils::pack_date($this->date_created),true);
		$criteria->compare('DATE(date_filled)', Utils::pack_date($this->date_filled), true);
		$criteria->compare('DATE(date_closed)', Utils::pack_date($this->date_closed), true);
                if($this->is_true_answer < 0)
                    $criteria->addCondition('is_true_answer IS NULL');
                else
                    $criteria->compare('is_true_answer', $this->is_true_answer, true);
		//$criteria->compare('check_question_id',$this->check_question_id);
		//$criteria->compare('check_question_order',$this->check_question_order);
		//$criteria->compare('check_question_group_id',$this->check_question_group_id);

                $criteria->order = "date_created DESC";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 * Save comment the model.
	 */
        public function addComment($comment)
        {
            $comment->application_id = $this->id;

            if(Yii::app()->getModule('user')->isAdmin() || Yii::app()->getModule('user')->isManager())
                $comment->role = ApplicationComment::ROLE_ADMIN;
            else
                $comment->role = ApplicationComment::ROLE_CLIENT;

            return $comment->save();
        }

        /**
	 * Get array applications.
	 */
        public function getApp()
        {
            $applRow = array();


            $applRow[Yii::app()->getModule('respondent')->t('Respondent')] = ($this->Respondent->fullNameWithPhone) ? $this->Respondent->fullNameWithPhone : Yii::t('app', "Anonymus respondentus");

            $sql = "SELECT tb1.answer_id, tb1.answer_text as open_answer, tb2.text as question_text, tb3.text as answer_text
            FROM tbl_application_answers tb1
            LEFT JOIN tbl_questions tb2 ON (tb1.question_id = tb2.id)
            LEFT JOIN tbl_answers tb3 ON (tb1.answer_id = tb3.id)
            WHERE tb1.application_id = :app_id";//да запрос ручками потому что через relations ужас как долго работает и забирает кучу ненужной инфы из базы данных
            $newAnswers = Yii::app()->db->createCommand($sql)->queryAll(true, array(":app_id"=>$this->id));

            if (sizeof($newAnswers) > 0)
            {
                foreach ($newAnswers as $answer) {
                    $textQ = $answer['question_text'];
                    if($answer['answer_id'] != '')// если у ответа есть id значит ответ закрытый
                    {
                        if(isset($applRow[$textQ]))//вариант с множественным ответов на вопрос
                        {
                            if($applRow[$textQ] != $answer['answer_text'])//почему-то бывает так что 2 одинаковых ответа на один закрытый вопрос
                                $applRow[$textQ] .= ' / '.$answer['answer_text'];//перечисляем ответы вопроса с множественным выбором
                        }
                        else
                            $applRow[$textQ] = $answer['answer_text'];
                    }
                    else // иначе ответ открытый
                    {
                        $applRow[$textQ] = $answer['open_answer'];
                    }
                }
                return $applRow;
            }
            else return false;




        }

        public function getCheckQuestion($group, $quiz){
            $checkQuestionParams = array();
            $checkQuestionParams['check_question_group_id'] = $group->id;
            // if we have manager's questions - use it
            $checkQuestions = DictCheckQuestions::model()->findAll("manager_id = :manager_id", array(":manager_id" => $quiz->manager_id));
            // else - use admin's questions
            if (!$checkQuestions) {
            	$checkQuestions = DictCheckQuestions::model()->findAll("manager_id = :manager_id", array(":manager_id" => 0));
            }
            if($checkQuestions){
                $indexCheckQuestions = rand(0, count($checkQuestions) - 1);
                $checkQuestionParams['check_question_id'] = $checkQuestions[$indexCheckQuestions]['id'];
                $indexOrderCheckQuestions = rand(0, count($group->questions));
                $checkQuestionParams['check_question_order'] = $indexOrderCheckQuestions + 1;

                return $checkQuestionParams;
            } else
                return array();
        }

        public function verificationTestQuestions(){
            if($this->check_answers_id && $this->checkAnswer['is_true']){
                $this->is_true_answer = 1;
                return true;
            }

            $this->is_true_answer = 0;
            $messenge = Yii::t('app', 'Automatic system testing found that the answer to the quiz {quiz} is spam',array('{quiz}'=>$this->quiz['title']));
            Utils::send_sms($this->Respondent['phone_number'], $messenge);
            return false;
        }

        public static function getPath(){
            return '/upload/answers/';
        }
}