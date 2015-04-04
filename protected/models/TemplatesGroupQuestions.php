<?php
class TemplatesGroupQuestions extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GroupQuestions the static model class
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
		return '{{templates_group_questions}}';
	}

        public function beforeDelete() {
            $this->deleteGroupQuestionsConnectionWithAnswer();
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
			array('template_id, orderby', 'numerical', 'integerOnly'=>true),
			array('condition_question_id', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, template_id, condition_question_id, orderby', 'safe', 'on'=>'search'),
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
			'conditionQuestion' => array(self::BELONGS_TO, 'TemplatesQuestion', 'condition_question_id'),
			'template' => array(self::BELONGS_TO, 'Templates', 'template_id'),
			'questions' => array(self::HAS_MANY, 'TemplatesQuestion', 'group_id','order'=>'questions.orderby'),
			'closeQuestions' => array(self::HAS_MANY, 'TemplatesQuestion', 'group_id','order'=>'orderby', 'condition' => 'type = "'.TemplatesQuestion::TYPE_CLOSE.'"'),
			'answers'  =>  array(self::MANY_MANY, 'TemplatesAnswers', 'tbl_templates_link_group_questions_answers(group_questions_id, answers_id)','index'=>'id'),
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
			'template_id' => 'Quiz',
			'condition_question_id' => 'Condition Question',
			'orderby' => 'orderby',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('template_id',$this->template_id);
		$criteria->compare('condition_question_id',$this->condition_question_id,true);
		$criteria->compare('orderby',$this->orderby);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 *
	 * @return Список, состоящий из пустых/новых группы вопросов, вопроса, ответа.
	 */
        public static function EmptyInit(){
            $groups = array(new GroupQuestions);
            foreach ($groups as $g => $group) {
                $group->questions = array(new Question);
                foreach ($group->questions as $q => $question) {
                    $question->answers = array(new Answer);
                }
            }
            return $groups;
        }

        /**
	 *  Проверяе группу вопросов на содержание закрытого вопроса в нем
	 * @return true/false.
	 */
        public function isCloseQuestions(){
            foreach ($this->questions as $q => $question) {
                if($question->type == Question::TYPE_CLOSE) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Check to exist connections with Answer and delete this connection
         */
        public function clearGroupQuestionsConnectionWithAnswer(){
            if($this->answers){
                $this->setAttribute('condition_question_id', null);
                $this->answers = array();
                $this->deleteGroupQuestionsConnectionWithAnswer();
            }
        }

        /**
         * Delete from table 'tbl_link_group_questions_answers' connect with Answer
         */
        public function deleteGroupQuestionsConnectionWithAnswer(){
            //Yii::app()->db->createCommand('DELETE FROM `tbl_link_group_questions_answers` WHERE `group_questions_id`=:group')->bindParam(":group",$this->id)->execute();
            Yii::app()->db->createCommand()->delete('tbl_templates_link_group_questions_answers', 'group_questions_id=:id', array(':id'=>$this->id));
        }
}