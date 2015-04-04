<?php

/**
 * This is the model class for table "{{application_answers}}".
 *
 * The followings are the available columns in table '{{application_answers}}':
 * @property integer $id
 * @property integer $application_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property string $answer_text
 *
 * The followings are the available model relations:
 * @property Applications $application
 */
class ApplicationAnswer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ApplicationAnswer the static model class
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
		return '{{application_answers}}';
	}

        public function beforeDelete()
        {
            if(parent::beforeDelete())
            {
                if($this->question['type'] == Question::TYPE_ANSWPHOTO){
                    $this->deleteImage(); // удалили модель? удаляем и файл
                }
                return true;
            }
            return false;
        }

        public function deleteImage()
        {
            if($this->answer_text){
                $imagePath=Yii::getPathOfAlias('webroot').Application::getPath().$this->answer_text;
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
			array('application_id', 'numerical', 'integerOnly'=>true),
			array('answer_text', 'length', 'max'=>500),
			array('question_id, answer_id', 'length', 'max'=>100),
                        array('answer_text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, application_id, question_id, answer_id, answer_text', 'safe', 'on'=>'search'),
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
			'application' => array(self::BELONGS_TO, 'Application', 'application_id'),
			'answer' => array(self::BELONGS_TO, 'Answer', 'answer_id'),
			'question' => array(self::BELONGS_TO, 'Question', 'question_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'application_id' => 'Application',
			'question_id' => 'Question',
			'answer_id' => 'Answer',
			'answer_text' => 'Answer Text',
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
		$criteria->compare('application_id',$this->application_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('answer_id',$this->answer_id);
		$criteria->compare('answer_text',$this->answer_text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}