<?php
class TemplatesQuestion extends CActiveRecord {
	const TYPE_OPEN = "open";
        const TYPE_CLOSE = "close";
        const TYPE_SEMICLOSE = "semiclose";
        const TYPE_SCALE_CLOSE = "scale_close";
        const TYPE_SCALE_SCORE = "scale_score";
        const TYPE_CLOSE_MULTISEL = "close_multiple_choice";
        const TYPE_ANSWPHOTO = "answer_photo";

        const API_TYPE_SINGLE = "single";
        const API_TYPE_KPI = "kpi";
        const API_TYPE_MULTIPLE = "multiple";

        /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Question the static model class
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
		return '{{templates_questions}}';
	}

        public function beforeSave()
        {
            if(parent::beforeSave())
            {
                if($this->isNewRecord)
                    $this->id = Utils::getGUID();
                return true;
            }
            return false;
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('text', 'required'),
			array('group_id, orderby, scaled_size, is_class', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>50),
			array('type', 'length', 'max'=>21),
			array('text', 'safe'),
			array('scaled_size', 'checkScaledSize'),
			array('type', 'checkSetAnswers'),
                        array('text, scaled_size','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, group_id, text, type, orderby, scaled_size, is_class', 'safe', 'on'=>'search'),
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
            'answers' => array(self::HAS_MANY, 'TemplatesAnswers', 'question_id','order'=>'answers.orderby','index'=>'id'),
            'pictures' => array(self::HAS_MANY, 'TemplatesQuestionMedia', 'question_id'),
            'group' => array(self::BELONGS_TO, 'TemplatesGroupQuestions', 'group_id'),
            'groupConditionQuestion' => array(self::HAS_ONE, 'TemplatesGroupQuestions', 'condition_question_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'group_id' => 'Group',
			'text' => Yii::t('app', 'Text question'),
			'type' => Yii::t('app', 'Type question'),
			'orderby' => 'orderby',
			'scaled_size' => Yii::t('app', 'Scaled Size'),
			'is_class' => Yii::t('app', 'Classifying question'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'TypeQuestion' => array(
                    self::TYPE_CLOSE => Yii::t('app', "Close question"),
                    self::TYPE_OPEN => Yii::t('app', "Open question"),
                    self::TYPE_SEMICLOSE => Yii::t('app', "Semiclose  question"),
                    self::TYPE_SCALE_CLOSE => Yii::t('app', "Scale close question"),
                    self::TYPE_SCALE_SCORE => Yii::t('app', "Scale score question"),
                    self::TYPE_CLOSE_MULTISEL => Yii::t('app', "Close multiple choice question"),
                    self::TYPE_ANSWPHOTO => Yii::t('app', "Answer photo question"),
                ),

                'quizTypes' => array(
                    self::TYPE_CLOSE => Yii::t('app', "Close question"),
                    self::TYPE_OPEN => Yii::t('app', "Open question"),
                    self::TYPE_SEMICLOSE => Yii::t('app', "Semiclose  question"),
                    self::TYPE_SCALE_CLOSE => Yii::t('app', "Scale close question"),
                    self::TYPE_SCALE_SCORE => Yii::t('app', "Scale score question"),
                    self::TYPE_CLOSE_MULTISEL => Yii::t('app', "Close multiple choice question"),
                ),

                'missionTypes' => array(
                	self::TYPE_ANSWPHOTO => Yii::t('app', "Answer photo question"),
                	self::TYPE_OPEN => Yii::t('app', "Report in free form"),
                	self::TYPE_CLOSE => Yii::t('app', "Report in variable answer"),
                	self::TYPE_CLOSE_MULTISEL => Yii::t('app', "Report in variables answers"),
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('orderby',$this->orderby);
		$criteria->compare('scaled_size',$this->scaled_size);
		$criteria->compare('is_class',$this->is_class);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 * Проверка типа вороса: если вопрос шкальный с баллами, атрибут "scaled_size" должно быть обязательно заполнено.
	 * This is the 'checkScaledSize' validator as declared in rules().
	 */
	public function checkScaledSize($attribute, $params)
	{
            if(!$this->hasErrors())  // we only want to authenticate when no input errors
            {
                $this->scaled_size = floatval($this->scaled_size);
                if($this->type == self::TYPE_SCALE_SCORE)
                    if(!$this->scaled_size){
                            $this->addError("scaled_size",Yii::t('app', "Scaled size not be empty."));
                    }
            }
	}

        /**
	 * Проверка типа вороса: если вопрос шкальный с баллами, атрибут "scaled_size" должно быть обязательно заполнено.
	 * This is the 'checkScaledSize' validator as declared in rules().
	 */
	public function checkSetAnswers($attribute, $params)
	{
            if(!$this->hasErrors())  // we only want to authenticate when no input errors
            {
                switch ($this->type){
                    case self::TYPE_CLOSE:
                    case self::TYPE_CLOSE_MULTISEL:
                    case self::TYPE_SEMICLOSE:
                    case self::TYPE_SCALE_CLOSE:
                        if(!$this->answers)
                                $this->addError("type", Yii::t('app', "Array answers not be empty."));
                        break;
                }
            }
	}

        /**
         * Получить статистику ответов на вопрос
         */
        public function getStatsAnswers($condition = '', $params = array()) {
            $answersAppl = array();
            $statsQ = array();
            $answersAppl = Yii::app()->db->createCommand()
                ->select('qa.answer_id, count(qa.id) AS count_answer, avg(qa.answer_text) AS sum_answer_text')
                ->from('{{application_answers}} qa')
                ->where('qa.question_id=:question'. ($condition ? ' AND '.$condition : ''), array_merge(array(':question'=>$this->id), $params))
                ->group('qa.answer_id')
                ->queryAll();

            foreach(array_keys($this->answers) as $i=>$answer_id){
                $statsQ[$answer_id] = 0;
            }

            if($answersAppl)
                foreach($answersAppl as $j=>$answAppl){
                    if(isset($statsQ[$answAppl['answer_id']])){
                        $answerM = Answer::model()->findByPk($answAppl['answer_id']);
                        if($answerM->question['type'] != Question::TYPE_SCALE_CLOSE)
                            $statsQ[$answAppl['answer_id']] = $answAppl['count_answer'];
                        else
                            $statsQ[$answAppl['answer_id']] = $answAppl['count_answer'] * $answerM['orderby'];
                    }else{
                        if($this->type == Question::TYPE_SCALE_SCORE)
                            $statsQ['other'] = intval(($answAppl['sum_answer_text'] * 100) / $this->scaled_size);
                        else
                            $statsQ['other'] = $answAppl['count_answer'];
                    }
                }
            else
                if($this->type == Question::TYPE_SCALE_SCORE)
                    $statsQ['other'] = 0;

            return $statsQ;
        }
}