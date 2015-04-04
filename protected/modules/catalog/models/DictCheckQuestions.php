<?php

/**
 * This is the model class for table "{{dict_check_questions}}".
 *
 * The followings are the available columns in table '{{dict_check_questions}}':
 * @property integer $id
 * @property string $text
 */
class DictCheckQuestions extends CActiveRecord
{
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictCheckQuestions the static model class
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
		return '{{dict_check_questions}}';
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
			array('id', 'answers'),
			array('text', 'safe'),
                        array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text', 'safe', 'on'=>'search'),
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
                    'answers' => array(self::HAS_MANY, 'DictCheckAnswers', 'question_id'),
                    'answerCount' => array(self::STAT, 'DictCheckAnswers', 'question_id'),
                    'respondentAnswer' => array(self::HAS_ONE, 'DictCheckAnswers', 'question_id'),
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
			'text' => Yii::t('app', 'Text question'),
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
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 * Answers check question.
	 * This is the 'answers' validator as declared in rules().
	 */
	public function answers($attribute,$params)
	{
            if(!$this->hasErrors())  // we only want to authenticate when no input errors
            {
                if(count($this->answers) > 0){
                    $isExistTrueAnswer = false;
                    foreach ($this->answers as $a => $answer) {
                        if($answer->is_true){
                            $isExistTrueAnswer = true;
                            break;
                        }
                    }
                    if(!$isExistTrueAnswer)
                        $this->addError('id',Yii::t('app', 'Not true answer.'));
                } else
                    $this->addError('id', Yii::t('app', 'Array answers not be empty.'));
            }
        }
}