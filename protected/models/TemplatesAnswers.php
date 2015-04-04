<?php
class TemplatesAnswers extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Answer the static model class
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
		return '{{templates_answers}}';
	}

        public function beforeSave()
        {
            if(parent::beforeSave())
            {
                if($this->checkScoreAnswer()){
                    if($this->isNewRecord)
                        $this->id = Utils::getGUID();
                    return true;
                }
            }
            return false;
        }

        public function beforeDelete() {
            $this->deleteAnswersConnectionWithGroupQuestions();
            return parent::beforeDelete();
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
			array('orderby', 'numerical', 'integerOnly'=>true),
			array('id, question_id', 'length', 'max'=>50),
			array('text', 'safe'),
                        array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
                        //array('orderby', 'checkScoreAnswer'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, question_id, text, orderby', 'safe', 'on'=>'search'),
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
                    'questionsGroups'  =>  array(self::MANY_MANY, 'TemplatesGroupQuestions', 'tbl_link_group_questions_answers(group_questions_id, answers_id)','index'=>'id'),
                    'audiences'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_classif_answers(target_audience_id, answers_id)','index'=>'id'),
                    'question' => array(self::BELONGS_TO, 'Question', 'question_id'),
                    'answerApplications' => array(self::HAS_MANY, 'ApplicationAnswer', 'answer_id'),
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
			'question_id' => 'Question',
			'text' => Yii::t('app', 'Text answer'),
			'orderby' => Yii::t('app', 'Scaled answer'),
		);
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
		$criteria->compare('question_id',$this->question_id,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('orderby',$this->orderby);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 * Проверка типа вороса: если вопрос шкальный закрытый, атрибут "orderby" для ответа должно быть обязательно заполнено.
	 * This is the 'checkScoreAnswer' validator as declared in rules().
	 */
	public function checkScoreAnswer()
	{
            if(!$this->hasErrors())  // we only want to authenticate when no input errors
            {
                if($this->question->type == Question::TYPE_SCALE_CLOSE){
                    $this->orderby = floatval($this->orderby);
                    if(!$this->orderby){
                            $this->addError("orderby", Yii::t('app', "Score for answer not be empty."));
                            return FALSE;
                    }
                }
            }
            return TRUE;
	}

        /**
         * Delete from table 'tbl_link_group_questions_answers' connect with GroupQuestions
         */
        public function deleteAnswersConnectionWithGroupQuestions(){
            //Yii::app()->db->createCommand('DELETE FROM `tbl_link_group_questions_answers` WHERE `group_questions_id`=:group')->bindParam(":group",$this->id)->execute();
            Yii::app()->db->createCommand()->delete('tbl_link_group_questions_answers', 'answers_id=:id', array(':id'=>$this->id));
        }

        /**
         * Set Answers legend
         */
        public static function legendAnswers($keys){
            $legendArr = array();
            foreach ($keys as $i => $id) {
                if($id == 'other')
                    array_push ($legendArr, Yii::t('app', 'Other'));
                else
                    array_push ($legendArr, Yii::t('app', 'Answer').' '.($i+1));
            }

            return $legendArr;
        }

}