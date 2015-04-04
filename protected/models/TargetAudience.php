<?php

/**
 * This is the model class for table "{{target_audience}}".
 *
 * The followings are the available columns in table '{{target_audience}}':
 * @property integer $id
 * @property integer $quiz_id
 * @property integer $age_from
 * @property integer $age_to
 * @property integer $income_from
 * @property integer $income_to
 * @property string $gender
 * @property string $marital_state
 * @property integer $minimal_user_state_id
 * @property integer $count_limit
 *
 * The followings are the available model relations:
 * @property UserStatus $minimalUserState
 * @property Quiz $quiz
 */
class TargetAudience extends CActiveRecord
{
        const GENDER_MALE = 'male';
        const GENDER_FEMALE = 'female';
        const GENDER_ANY = 'any';

	const MARITAL_STATE_SINGLE = 'single';
	const MARITAL_STATE_MARRIED = 'married';
	const MARITAL_STATE_DEVORCED = 'divorced';
	const MARITAL_STATE_ANY = 'any';
    const RESPONDENT_PRICE = 100;

        public $tableLinkEducations = '{{link_education_target_audience}}';
	public $tableLinkScopes = '{{link_target_audience_scope}}';
	public $tableLinkPositions = '{{link_target_audience_job_position}}';
        public $tableLinkClassfAnswers = '{{link_target_audience_classif_answers}}';
	public $tableLinkCountries = '{{link_target_audience_country}}';
	public $tableLinkCities = '{{link_target_audience_city}}';
	public $tableLinkGroupRespondents = '{{link_target_audience_group_respondents}}';


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetAudience the static model class
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
		return '{{target_audience}}';
	}

    protected function afterSave() {
        if ($this->quiz->state == Quiz::STATE_WORK) {
            $this->sendPushNotification();
        }

        parent::afterSave();
    }


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quiz_id, age_from, age_to, income_from, income_to, minimal_user_state_id, count_limit', 'numerical', 'integerOnly'=>true),
			array('gender', 'length', 'max'=>6),
			array('marital_state', 'length', 'max'=>8),
                        array('age_from, age_to, income_from, income_to, count_limit','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, quiz_id, age_from, age_to, income_from, income_to, gender, marital_state, minimal_user_state_id, count_limit', 'safe', 'on'=>'search'),
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
			'minimalUserState' => array(self::BELONGS_TO, 'Status', 'minimal_user_state_id'),
			'quiz' => array(self::BELONGS_TO, 'Quiz', 'quiz_id'),
                        'educations'  =>  array(self::MANY_MANY, 'DictEducation', 'tbl_link_education_target_audience(target_audience_id, education_id)','index'=>'dict_education_id'),
                        'scopes'  =>  array(self::MANY_MANY, 'DictScope', 'tbl_link_target_audience_scope(target_audience_id, scope_id)','index'=>'dict_scope_id'),
                        'job_position'  =>  array(self::MANY_MANY, 'DictJobPosition', 'tbl_link_target_audience_job_position(target_audience_id, job_position_id)','index'=>'dict_job_position_id'),
                        'countries'  =>  array(self::MANY_MANY, 'DictCountry', 'tbl_link_target_audience_country(target_audience_id, country_id)','index'=>'dict_country_id'),
                        'cities'  =>  array(self::MANY_MANY, 'DictCity', 'tbl_link_target_audience_city(target_audience_id, city_id)','index'=>'dict_city_id'),
                        'classfAnswers'  =>  array(self::MANY_MANY, 'Answer', 'tbl_link_target_audience_classif_answers(target_audience_id, answers_id)','index'=>'id'),
                        'groupsRespondents'  =>  array(self::MANY_MANY, 'GroupRespondents', 'tbl_link_target_audience_group_respondents(target_audience_id, group_id)','index'=>'id'),
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
			'quiz_id' => Yii::t('app', 'Quiz'),
			'age' => Yii::t('app', 'Age'),
			'income' => Yii::t('app', 'Income'),
			'gender' => Yii::t('app', 'Gender'),
			'marital_state' => Yii::t('app', 'Marital State'),
			'minimal_user_state_id' => Yii::t('app', 'Minimal User State'),
			'count_limit' => Yii::t('app', 'Count Limit'),
			'educations' => Yii::t('app', 'Educations'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'GenderAudience' => array(
                    self::GENDER_ANY => Yii::t('app', 'Any'),
                    self::GENDER_MALE => Yii::t('app', 'Male'),
                    self::GENDER_FEMALE => Yii::t('app', 'Female'),
                ),
                'MaritalStateAudience' => array(
                    self::MARITAL_STATE_ANY => Yii::t('app', 'Any'),
                    self::MARITAL_STATE_SINGLE => Yii::t('app', 'Single Marital State'),
                    self::MARITAL_STATE_MARRIED => Yii::t('app', 'Married Marital State'),
                    self::MARITAL_STATE_DEVORCED => Yii::t('app', 'Devorced Marital State'),
                ),
            );

            if (isset($code))
                return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
            else
                return isset($_items[$type]) ? $_items[$type] : false;
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
		$criteria->compare('quiz_id',$this->quiz_id);
		$criteria->compare('age_from',$this->age_from);
		$criteria->compare('age_to',$this->age_to);
		$criteria->compare('income_from',$this->income_from);
		$criteria->compare('income_to',$this->income_to);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('marital_state',$this->marital_state,true);
		$criteria->compare('minimal_user_state_id',$this->minimal_user_state_id);
		$criteria->compare('count_limit',$this->count_limit);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

         /**
         * Delete from table connect with Target Audience
         */
        //public function deleteConnectionWithTargetAudience($table, $tableJoin, $columnJoin, $arrayIds){
        public function deleteConnectionWithTargetAudience($table){
            /*$strIds = implode(',', $arrayIds);
            $audience = $this->id;
            $sql = "DELETE t1.* FROM {$table} t1
                        INNER JOIN (SELECT r.id FROM {$tableJoin} t2
                            LEFT JOIN {$table} t3 ON t3.{$columnJoin}=t2.id
                            WHERE t3.target_audience_id=:audience AND t2.id NOT IN (:ids)) t4
                        ON t1.{$columnJoin}=t4.id
                        WHERE target_audience_id=:audience";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":audience", $audience);
            $command->bindParam(":ids", $strIds);
            $command->execute();*/


            $audience = $this->id;
            $sql = 'DELETE FROM '.$table.' WHERE target_audience_id=:audience';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":audience", $audience);
            $command->execute();
        }

        public function concatAge(){ return $this->age_from.'—'.$this->age_to; }
        public function concatIncome(){ return $this->income_from.' — '.$this->income_to; }

         /**
         * Delete from table connect with Target Audience
         */
        public function getRespondents(){
            $criteria = new CDbCriteria();
            $criteriaCondition = '';
            $criteriaParams = array();

            $currentTimestamp = time();
            # Age from
            if($this->age_from){
                $condAgeFrom = strtotime('-'.$this->age_from.' years', $currentTimestamp);
                $dateAgeFrom = date('Y-m-d', $condAgeFrom);
                $criteriaCondition .= "birth_date <= :dateAgeFrom AND ";
                $criteriaParams[':dateAgeFrom'] = $dateAgeFrom;
            }

            # Age to
            if($this->age_to){
                $condAgeTo = strtotime('-'.$this->age_to.' years', $currentTimestamp);
                $dateAgeTo = date('Y-m-d', $condAgeTo);
                $criteriaCondition .= "birth_date >= :dateAgeTo AND ";
                $criteriaParams[':dateAgeTo'] = $dateAgeTo;
            }

            # Income from
            if($this->income_from){
                $criteriaCondition .= "income >= :incomeFrom AND ";
                $criteriaParams[':incomeFrom'] = $this->income_from;
            }

            # Income to
            if($this->income_to){
                $criteriaCondition .= "income <= :incomeTo AND ";
                $criteriaParams[':incomeTo'] = $this->income_to;
            }

            # Gender
            if($this->gender != TargetAudience::GENDER_ANY){
                $criteriaCondition .= "sex = :gender AND ";
                $criteriaParams[':gender'] = $this->gender;
            }

            # Marital state
            if($this->marital_state && $this->marital_state != TargetAudience::MARITAL_STATE_ANY){
                $criteriaCondition .= "marital_state = :maritalState AND ";
                $criteriaParams[':maritalState'] = $this->marital_state;
            }

            # Minimal User State
            if($this->minimal_user_state_id){
                $criteriaCondition .= "state_id >= 1 AND ";
                #$criteriaCondition .= "state_id >= :state AND ";
                #$criteriaParams[':state'] = $this->minimalUserState->id;
            }

            # Educations
            if($this->educations){
                $conditionEducation = 'education_id IN (';
                foreach($this->educations as $id=>$education){
                    $conditionEducation .= $id.',';
                }
                $conditionEducation = Utils::cut_last($conditionEducation).')';
                //$criteriaCondition .= $condition." AND ";
            }

            # Job position
            if($this->job_position){
                $condition = 'position_id IN (';
                foreach($this->job_position as $id=>$position){
                    $condition .= $id.',';
                }
                $condition = Utils::cut_last($condition).')';
                $criteriaCondition .= $condition." AND ";
            }

            # Scopes
            if($this->scopes){
                $condition = 'scope_id IN (';
                foreach($this->scopes as $id=>$scope){
                    $condition .= $id.',';
                }
                $condition = Utils::cut_last($condition).')';
                $criteriaCondition .= $condition." AND ";
            }

            # Classification answers
            if($this->classfAnswers){
                $condition = 't.id IN (0'.',';
                foreach($this->classfAnswers as $id=>$answer){
                    foreach($answer->answerApplications as $idApp=>$applicationAnswer){
                        if($applicationAnswer->application['respondent_id'])
                            $condition .= $applicationAnswer->application['respondent_id'].',';
                    }
                }
                $condition = Utils::cut_last($condition).')';
                $criteriaCondition .= $condition." AND ";
            }

            # Countries
            if($this->countries){
                $condition = 'country_id IN (';
                foreach($this->countries as $id=>$country){
                    $condition .= $id.',';
                }
                $condition = Utils::cut_last($condition).')';
                $criteriaCondition .= $condition." AND ";
            }

            # Cities
            if($this->cities){
                $condition = 'city_id IN (';
                foreach($this->cities as $id=>$city){
                    $condition .= $id.',';
                }
                $condition = Utils::cut_last($condition).')';
                $criteriaCondition .= $condition." AND ";
            }

            # Groups respondents
            if($this->groupsRespondents){
                $conditionGroups = 'group_respondents_id IN (';
                foreach($this->groupsRespondents as $id=>$group){
                    $conditionGroups .= $id.',';
                }
                $conditionGroups = Utils::cut_last($conditionGroups).')';
                //$criteriaCondition .= $condition." AND ";
            } else
                $conditionGroups = 'group_respondents_id IS NULL';

            $criteriaCondition = Utils::cut_last($criteriaCondition, 5);

            $criteria->addCondition($criteriaCondition);

            if(isset($conditionEducation) && $conditionEducation)
                $criteria->with['educations'] = array(
                    'select'=>false,
                    'condition'=>$conditionEducation,
                );

            if(isset($conditionGroups) && $conditionGroups)
                $criteria->with['groups'] = array(
                    'select'=>false,
                    'condition'=>$conditionGroups,
                );

            $criteria->index = 'id';
            $criteria->params = $criteriaParams;
            $criteria->compare('phone_is_confirmed', 1);
            if(isset($this->quiz->money) && $this->quiz->money > 0)
                $criteria->compare('payable', 1);

            if($this->count_limit)
                $criteria->limit = $this->count_limit;
            $respondents = Respondent::model()->findAll($criteria);
            return $respondents;
        }


		/* if user have base audience in all list of audiences */
        public static function haveBaseAudience($quiz) {
			$base = false;
			foreach($quiz->audience as $audience) {
				$sql = "
					SELECT * FROM `tbl_link_target_audience_group_respondents`
					WHERE
						`target_audience_id` = :id
				";
				$command	=	Yii::app()->db->createCommand($sql);
				$command->bindValue(':id', $audience->id);

				// if we don't have group in list, orderer use all base
				if(!$command->query()->read()) {
				   	$base = true;
				}
			}

			return $base;
        }

        /**
         * Отправляем Push-уведомление новой аудитории существующего опроса
         */
        public function sendPushNotification() {
            $ios = array();
            $android = array();
            foreach ($this->getRespondents() as $i => $respondent) {
                if ($respondent->sessions['device_token']) {
                    if ($respondent->sessions['device_type'] == "android") {
                        $android[] = $respondent->sessions['device_token'];
                    }
                    else {
                        $ios[] = $respondent->sessions['device_token'];
                    }
                }
            }

            $messenge = Yii::t('app', 'New quiz').' '.$this->quiz->title;
            Yii::app()->getModule('respondent')->sendNotifications($messenge, $ios);
            
            $gcm = Yii::app()->gcm;
            $gcm->send($android, $messenge);
        }
}