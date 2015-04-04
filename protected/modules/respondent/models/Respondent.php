<?php

/**
 * This is the model class for table "{{respondents}}".
 *
 * The followings are the available columns in table '{{respondents}}':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $phone_code
 * @property string $phone_code_expdate
 * @property integer $phone_is_confirmed
 * @property string $email_actual
 * @property string $email_new
 * @property string $email_code
 * @property string $email_code_expdate
 * @property string $social_type
 * @property string $social_key
 * @property string $password
 * @property string $sex
 * @property string $marital_state
 * @property string $birth_date
 * @property integer $position_id
 * @property integer $scope_id
 * @property integer $country_id
 * @property integer $city_id
 * @property integer $income
 * @property double $money
 * @property double $karma
 * @property integer $state_id
 *
 * The followings are the available model relations:
 * @property DictCity $city
 * @property DictCountry $country
 * @property DictJobPosition $position
 * @property DictScope $scope
 * @property RespondentsStatuses $state
 * @property RespondentsPayments[] $respondentsPayments
 * @property Sessions $sessions
 */
class Respondent extends CActiveRecord
{
        const GENDER_MALE = 'male';
        const GENDER_FEMALE = 'female';
        const NONE = 'none';

	const MARITAL_STATE_SINGLE = 'single';
	const MARITAL_STATE_MARRIED = 'married';
	const MARITAL_STATE_DEVORCED = 'divorced';

        const SOCIAL_TYPE_VK = 1;
        const SOCIAL_TYPE_FACEBOOK = 2;
        const SOCIAL_TYPE_TWITTER = 3;
        const SOCIAL_TYPE_ODNOKLASSNIKI = 4;

        const PHONE_CODE_LIFETIME = 3;
        const EMAIL_CODE_LIFETIME = 3;

        public $image = null;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Respondent the static model class
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
		return Yii::app()->getModule('respondent')->tableRespondents;
	}

        public function beforeSave()
        {
            if(parent::beforeSave())
            {
                // THIS is how you capture those uploaded images: remember that in your CMultiFile widget, you set 'name' => 'images'
                // proceed if the images have been set
                if (isset($this->image)) {
                    $name = Yii::getPathOfAlias('webroot').self::getPathAvatar().$this->avatar;
                    $this->deleteImage();
                    // go through each uploaded image
                    self::saveImage($this->image, $name);
                }
                return true;
            }
            return false;
        }

        /**
         * This is invoked after the record is saved.
         */
        protected function afterSave()
        {
                parent::afterSave();
                if($this->sex != self::NONE){
                    $log_captcha = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from('{{captcha}}')
                            ->where('respondent_id=:id OR ((respondent_id IS NULL AND date_created < DATE_SUB(NOW(),INTERVAL 1 HOUR))) OR ((date_created < DATE_SUB(NOW(),INTERVAL 1 DAY)))', array(':id'=>$this->id))
                            ->queryAll();

                    foreach($log_captcha as $i=>$item){
                        $img_name = Yii::getPathOfAlias('webroot') . $item['full_path'];
                        if(is_file($img_name))
                            unlink($img_name);
                        Yii::app()->db->createCommand()->delete('{{captcha}}', 'id=:id', array(':id'=>$item['id']));
                    }

                    Yii::app()->db->createCommand()->delete('{{history_respondent}}','respondent_id=:respondent AND key_action="code_phone" AND action_do="edit"', array(':respondent'=>$this->id));
                }
        }

        public function beforeDelete()
        {
            if(parent::beforeDelete())
            {
                $this->deleteImage(); // удалили модель? удаляем и файл
                return true;
            }
            return false;
        }

        public static function saveImage($image, $name)
        {
            if ($image->saveAs($name)) {
                // add it to the main model now
                $image = Yii::app()->image->load($name);
                list($width, $height, $type, $attr) = getimagesize($name);
                if($width != $height){
                    if($width > $height)
                        $width = $height;
                    elseif($width < $height)
                        $height = $width;
                    $image->crop($width, $height);
                }
                $image->resize(240, 240);
                $image->save();
                return true;
            }else
                return false;
        }

        public function deleteImage()
        {
            if($this->avatar){
                $imagePath=Yii::getPathOfAlias('webroot').self::getPathAvatar().$this->avatar;
                if(is_file($imagePath))
                    unlink($imagePath);
            }
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone_is_confirmed, position_id, scope_id, country_id, city_id, income, state_id, blocked, payable', 'numerical', 'integerOnly'=>true),
			array('money, karma', 'numerical'),
			array('first_name, last_name, phone_number, phone_code_expdate, email_code_expdate', 'length', 'max'=>20),
			array('phone_code, email_actual, email_new, email_code, social_key, password, avatar', 'length', 'max'=>128),
            array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true, 'safe'=>false),
			array('social_type', 'length', 'max'=>16),
			array('sex', 'length', 'max'=>6),
			array('marital_state', 'length', 'max'=>8),
            array('first_name, last_name, phone_number, phone_code, phone_code_expdate, email_actual, email_new, email_code, email_code_expdate, social_key, password, income, money, karma, avatar, password_code','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, first_name, last_name, phone_number, phone_code, phone_code_expdate, phone_is_confirmed, email_actual, email_new, email_code, email_code_expdate, social_type, social_key, password, sex, marital_state, birth_date, position_id, scope_id, country_id, city_id, income, money, karma, state_id, blocked, payable', 'safe', 'on'=>'search'),
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
			'city' => array(self::BELONGS_TO, 'DictCity', 'city_id'),
			'country' => array(self::BELONGS_TO, 'DictCountry', 'country_id'),
			'position' => array(self::BELONGS_TO, 'DictJobPosition', 'position_id'),
			'scope' => array(self::BELONGS_TO, 'DictScope', 'scope_id'),
			'state' => array(self::BELONGS_TO, 'Status', 'state_id'),
			'respondentsPayments' => array(self::HAS_MANY, 'RespondentsPayments', 'respondent_id'),
			'sessions' => array(self::HAS_ONE, 'Session', 'respondent_id'),
			'educations' => array(self::MANY_MANY, 'DictEducation', 'tbl_link_respondents_educations(respondent_id, education_id)','index'=>'dict_education_id'),
            'groups' => array(self::MANY_MANY, 'GroupRespondents', 'tbl_link_users_group_respondents(respondents_id, group_respondents_id)','index'=>'id','order'=>'groups.id'),
            'applications' => array(self::HAS_MANY, 'Application', 'respondent_id'),
            'answers' => array(self::HAS_MANY, 'ApplicationAnswer', array('respondent_id'=>'application_id'), 'through'=>'applications'),

		);
	}

        public function behaviors() {
            return array(
                'withRelated'=>array(
                    'class'=>'ext.withRelated.WithRelatedBehavior',
                ),
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'first_name' => RespondentModule::t('First Name'),
			'last_name' => RespondentModule::t('Last Name'),
			'phone_number' => RespondentModule::t('Phone Number'),
			'phone_code' => RespondentModule::t('Phone Code'),
			'phone_code_expdate' => RespondentModule::t('Phone Code Expdate'),
			'phone_is_confirmed' => RespondentModule::t('Phone Is Confirmed'),
			'email_actual' => RespondentModule::t('Email Actual'),
			'email_new' => RespondentModule::t('Email New'),
			'email_code' => RespondentModule::t('Email Code'),
			'email_code_expdate' => RespondentModule::t('Email Code Expdate'),
			'social_type' => RespondentModule::t('Social Type'),
			'social_key' => RespondentModule::t('Social Key'),
			'password' => RespondentModule::t('Password'),
			'sex' => RespondentModule::t('Sex'),
			'marital_state' => RespondentModule::t('Marital State'),
			'birth_date' => RespondentModule::t('Birth Date'),
			'position_id' => RespondentModule::t('Position'),
			'scope_id' => RespondentModule::t('Scope'),
			'country_id' => RespondentModule::t('Country'),
			'city_id' => RespondentModule::t('City'),
			'income' => RespondentModule::t('Income'),
			'money' => RespondentModule::t('Money'),
			'karma' => RespondentModule::t('Karma'),
			'state_id' => RespondentModule::t('State'),
			'avatar' => RespondentModule::t('Avatar'),
			'blocked' => RespondentModule::t('Blocked respondent'),
			'payable' => RespondentModule::t('Payable respondent'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'RespondentGender' => array(
				self::NONE => RespondentModule::t('None'),
				self::GENDER_MALE => RespondentModule::t('Male'),
				self::GENDER_FEMALE => RespondentModule::t('Female'),
			),
			'RespondentMaritalState' => array(
				self::NONE => RespondentModule::t('None'),
				self::MARITAL_STATE_SINGLE => RespondentModule::t('Single marital state'),
				self::MARITAL_STATE_MARRIED => RespondentModule::t('Married marital state'),
				self::MARITAL_STATE_DEVORCED => RespondentModule::t('Devorced marital state'),
			),
                        'RespondentSocial' => array(
				self::NONE => RespondentModule::t('None'),
				self::SOCIAL_TYPE_VK => RespondentModule::t('vk.com'),
				self::SOCIAL_TYPE_FACEBOOK => RespondentModule::t('facebook.com'),
				self::SOCIAL_TYPE_TWITTER => RespondentModule::t('twitter.com'),
				self::SOCIAL_TYPE_ODNOKLASSNIKI => RespondentModule::t('odnoklassniki.ru'),
			),
                        'PhoneConfirmedStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}

        public function getFullName(){
            return $this->last_name.' '.$this->first_name;
        }

        public function getFullNameWithPhone(){
            return $this->last_name.' '.$this->first_name.' '.SubStr($this->phone_number, 0, -4)."XXXX";;
        }

        public static function getPathAvatar(){
            //return Yii::getPathOfAlias('webroot').'/images/questions/';
            return '/upload/avatar/';
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

		$criteria->compare('id',$this->id);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('phone_number',$this->phone_number,true);
		$criteria->compare('phone_code',$this->phone_code,true);
		$criteria->compare('phone_code_expdate',$this->phone_code_expdate,true);
		$criteria->compare('phone_is_confirmed',$this->phone_is_confirmed);
		$criteria->compare('email_actual',$this->email_actual,true);
		$criteria->compare('email_new',$this->email_new,true);
		$criteria->compare('email_code',$this->email_code,true);
		$criteria->compare('email_code_expdate',$this->email_code_expdate,true);
		$criteria->compare('social_type',$this->social_type,true);
		$criteria->compare('social_key',$this->social_key,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('sex',$this->sex,true);
		$criteria->compare('marital_state',$this->marital_state,true);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('position_id',$this->position_id);
		$criteria->compare('scope_id',$this->scope_id);
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('income',$this->income);
		$criteria->compare('money',$this->money);
		$criteria->compare('karma',$this->karma);
		$criteria->compare('state_id',$this->state_id);
                if($this->blocked)
                    $criteria->compare('blocked',$this->blocked);
                else
                    $criteria->addCondition('blocked IS NULL OR blocked = 0');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
         * Delete from table connect with DictEducations
         */
        public function deleteConnectionWithEducations(){
            $respondent_id = $this->id;
            $sql = 'DELETE FROM '.Yii::app()->getModule('respondent')->tableRespondentsEducations.' WHERE respondent_id=:respondent';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":respondent", $respondent_id);
            $command->execute();
            //Yii::app()->db->createCommand('DELETE FROM `tbl_users_clients` WHERE users_id=:user')->bindParam(":user",$model->id)->execute();
        }

        /**
         * Return list quiz of models
         */
        public function listQuizs($conditionArr = array(), $paramsArr = array()){
            $criteria = new CDbCriteria();
            $criteria->addCondition('(date(now()) - INTERVAL age_from YEAR >= :age AND date(now()) - INTERVAL age_to YEAR <= :age) OR (age_from = 0 AND age_to=0)');
            $criteria->addCondition('(income_from <= :income AND income_to >= :income) OR (income_from = 0 AND income_to = 0)');
            $criteria->addCondition('gender = :gender OR gender = :any');
            $criteria->addCondition('marital_state = :marital OR marital_state = :any');
            $criteria->addCondition('minimal_user_state_id <= :state');
            $criteria->with = array('educations', 'scopes', 'job_position', 'countries', 'cities','quiz','groupsRespondents');
            //$criteria->addCondition('quiz.state=:stateQuizWork OR quiz.state=:stateQuizFill');
            $criteria->addCondition('quiz.date_start IS NOT NULL AND date(quiz.date_start) <= date(now())');
            $criteria->addCondition('quiz.archive IS NULL OR quiz.archive=0');
            foreach ($conditionArr as $c => $cond) {
                $criteria->addCondition($cond);
            }
            $criteria->params = array_merge(array(
                ':age'=>$this->birth_date,
                ':income'=>$this->income,
                ':gender'=>$this->sex,
                ':any'=>TargetAudience::GENDER_ANY,
                ':marital'=>$this->marital_state,
                ':state'=>$this->state_id,
                /*':scope'=>$this->scope_id,
                ':position'=>$this->position_id,
                ':country'=>$this->country_id,
                ':city'=>$this->city_id,*/
                //':stateQuizWork'=>Quiz::STATE_WORK,
                //':stateQuizFill'=>Quiz::STATE_FILL,
            ), $paramsArr);

            if($this->scope_id){
                $criteria->addCondition('scopes.dict_scope_id IS NULL OR scopes.dict_scope_id = :scope');
                $criteria->params = array_merge($criteria->params, array(':scope'=>$this->scope_id));
            }

            if($this->position_id){
                $criteria->addCondition('job_position.dict_job_position_id IS NULL OR job_position.dict_job_position_id = :position');
                $criteria->params = array_merge($criteria->params, array(':position'=>$this->position_id));
            }
            if($this->country_id){
                $criteria->addCondition('countries.dict_country_id IS NULL OR countries.dict_country_id = :country');
                $criteria->params = array_merge($criteria->params, array(':country'=>$this->country_id));
            }
            if($this->city_id){
                $criteria->addCondition('cities.dict_city_id IS NULL OR cities.dict_city_id = :city');
                $criteria->params = array_merge($criteria->params, array(':city'=>$this->city_id));
            }

            if($this->educations)
                $criteria->addCondition('educations.dict_education_id IS NULL OR educations.dict_education_id IN ('.implode(',',array_keys($this->educations)).')');

            if($this->groups)
                $criteria->addCondition('groupsRespondents.id IS NULL OR groupsRespondents.id IN ('.implode(',',array_keys($this->groups)).')');
            else
                $criteria->addCondition('groupsRespondents.id IS NULL');

            $audience = TargetAudience::model()->findAll($criteria);

            $quizs = array();
            foreach ($audience as $id => $value) {
                $quiz = $value->quiz;
                if(($this->payable == 0 && $quiz->money == 0) || $this->payable == 1)
                    $quizs[$value['quiz_id']] = $quiz;

            }

            return $quizs;
        }

        public function addProfit($quiz_id)
	{
            $respondent = $this;
            $respondentStatus = $respondent->state;
            $quiz = Quiz::model()->findByPk($quiz_id);

            $respondent->money += $quiz['money']/* * $respondentStatus['multiplicator'])*/;
            $respondent->karma += ($quiz['karma'] * $respondentStatus['multiplicator']);

            $criteria = new CDbCriteria;
            $criteria->select = 'id';
            $criteria->condition = 'karma <= :karma';
            $criteria->order = 'karma DESC';
            $criteria->params = array('karma'=>$respondent->karma);
            $newRespondentStatus = Status::model()->find($criteria);
            if($respondent->state_id != $newRespondentStatus['id'])
                $respondent->state_id = $newRespondentStatus['id'];
            $respondent->save();
        }

        public function getCountQuiz(){
            $quizs = $this->listQuizs();

            $results = Yii::app()->db->createCommand()
                    ->select('tq.type, ta.state, count(tq.quiz_id) as cquiz')
                    ->from(Quiz::model()->tableName().' tq')
                    ->leftJoin(Application::model()->tableName().' ta', 'tq.quiz_id=ta.quiz_id AND ta.respondent_id=:respondent', array(':respondent'=>$this->id))
                    //->where(array('AND', array('in', 'tq.quiz_id', array_keys($quizs)), array('AND', array('OR', 'tq.state="'.Quiz::STATE_WORK.'"', array('AND','tq.state="'.Quiz::STATE_FILL.'"','ta.id IS NOT NULL')), 'tq.archive!=1')))
                    ->where(array('AND', array('in', 'tq.quiz_id', array_keys($quizs)), array('OR', 'tq.state="'.Quiz::STATE_WORK.'"', 'tq.state="'.Quiz::STATE_FILL.'"')))
                    ->group(array('tq.type', 'ta.state'))
                    ->queryAll();

            if($results)
                return $results;

            return array();
        }

        public function showCountQuiz(){
            $result = array(Application::STATE_AVAILABLE=>0, Application::STATE_TODO=>0, Application::STATE_CLOSE=>0);
            foreach ($this->getCountQuiz() as $id => $value) {
                switch ($value['state']) {
                    case Application::STATE_CLOSE:
                        $result[Application::STATE_CLOSE] += $value['cquiz'];
                        break;
                    case Application::STATE_TODO:
                    case Application::STATE_APPEAL:
                    case Application::STATE_DONE:
                    case Application::STATE_REJECT:
                        $result[Application::STATE_TODO] += $value['cquiz'];
                        break;
                    default:
                        if(!$value['state'])
                            $result[Application::STATE_AVAILABLE] += $value['cquiz'];
                        break;
                }
            }
            $text = CHtml::tag('a', array('class'=>'badge badge-important', 'rel'=>'tooltip', 'data-original-title'=>RespondentModule::t('Quiz unclimbed')), $result[Application::STATE_AVAILABLE]).'&nbsp;'.
                    CHtml::tag('a', array('class'=>'badge badge-warning', 'rel'=>'tooltip', 'data-original-title'=>RespondentModule::t('Quiz current')), $result[Application::STATE_TODO]).'&nbsp;'.
                    CHtml::tag('a', array('class'=>'badge badge-success', 'rel'=>'tooltip', 'data-original-title'=>RespondentModule::t('Quiz passed')), $result[Application::STATE_CLOSE]);
            return $text;
        }

        public function getClassificQuestions(){
            $respondentAnswerOnApplication = ApplicationAnswer::model()->with('question', 'application', 'answer')->findAll('question.is_class=1 AND application.state=:state AND application.respondent_id=:respondent', array(':state'=>Application::STATE_CLOSE, ':respondent'=>$this->id));
            $listArr = array();
            foreach ($respondentAnswerOnApplication as $a=>$value) {
                array_push($listArr, array('question'=>$value->question['text'], 'answer'=>$value->answer['text']));
            }
            return $listArr;

        }

        /**
         * @param boolean $is_confirm
         * @return - html-текст в зависимости от значения @param
         */
        public function getIndicatorConfirmPhone(){
            if($this->phone_is_confirmed)
                $htmlText = CHtml::link (CHtml::tag ('i', array('class'=>'icon-ok icon-white')), '#', array('rel'=>'tooltip','title'=>RespondentModule::t('Respondent logged in'),'class'=>'badge badge-success'));
            else
                $htmlText = CHtml::link (CHtml::tag ('i', array('class'=>'icon-remove icon-white')), '', array('rel'=>'tooltip','title'=>RespondentModule::t('Respondent did not register in the system'),'class'=>'badge badge-important'));
            return $htmlText;
        }

        /**
         * Delete connect Group respondents with Respondent
         */
        public function deleteConnectionWithGroup(){
            $respondent = $this->id;
            $table = 'tbl_link_users_group_respondents';
            $sql = 'DELETE FROM '.$table.' WHERE respondents_id=:respondent';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":respondent", $respondent);
            $command->execute();
        }

        /*
         * Входит ли респондент в группы
         */
        public function isEntersGroup($groups = null){
            if($groups)
                if(count($this->groups(array('select'=>'DISTINCT groups.id', 'condition'=>'groups.id IN (:arrayGroups)', 'params'=>array(':arrayGroups'=>implode(',', $groups))))) > 0)
                    return true;
            elseif(count($this->groups) > 0)
                return true;
            return false;
        }

}