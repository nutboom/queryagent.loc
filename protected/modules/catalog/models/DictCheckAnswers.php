<?php

/**
 * This is the model class for table "{{dict_check_answers}}".
 *
 * The followings are the available columns in table '{{dict_check_answers}}':
 * @property integer $id
 * @property integer $question_id
 * @property string $text
 * @property integer $is_true
 */
class DictCheckAnswers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictCheckAnswers the static model class
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
		return '{{dict_check_answers}}';
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
			array('is_true', 'numerical', 'integerOnly'=>true),
			array('question_id, text', 'safe'),
                        array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, question_id, text, is_true', 'safe', 'on'=>'search'),
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
                    'question' => array(self::BELONGS_TO, 'DictCheckQuestions', 'question_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'question_id' => Yii::t('app', 'Question'),
			'text' => Yii::t('app', 'Text answer'),
			'is_true' =>Yii::t('app',  'Is true answer'),
			'text[]' => Yii::t('app', 'Text answer'),
			'is_true[]' =>Yii::t('app',  'Is true answer'),
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
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('is_true',$this->is_true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
     * Get Json structure
     */

    public static function getJsonStructureAnswers($arrAnwers){
        $jsonData['answerArray'] = array();
        if($arrAnwers)
            foreach ($arrAnwers as $a => $answer){
                if($answer->attributes['id'])
                    $jsonData['answerArray'][$a]['DictCheckAnswers[id][]'] = $answer->attributes['id'];
                $jsonData['answerArray'][$a]['DictCheckAnswers[text][]'] = $answer->attributes['text'];
                $jsonData['answerArray'][$a]['DictCheckAnswers[is_true][]'] = $answer->attributes['is_true'];
            }
        return json_encode($jsonData);
    }
}