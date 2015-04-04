<?php

/**
 * This is the model class for table "{{quiz_comments}}".
 *
 * The followings are the available columns in table '{{quiz_comments}}':
 * @property integer $id
 * @property integer $respondent_id
 * @property integer $quiz_id
 * @property string $date_created
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Quiz $quiz
 * @property Respondents $respondent
 */
class QuizComment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QuizComment the static model class
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
		return '{{quiz_comments}}';
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
                if($this->isNewRecord)
                    $this->date_created = date('Y-m-d H:i:s');
                return true;
            } else
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
			array('respondent_id, quiz_id', 'numerical', 'integerOnly'=>true),
			array('date_created, text', 'safe'),
                        array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, respondent_id, quiz_id, date_created, text', 'safe', 'on'=>'search'),
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
			'quiz' => array(self::BELONGS_TO, 'Quiz', 'quiz_id'),
			'respondent' => array(self::BELONGS_TO, 'Respondent', 'respondent_id'),
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
			'quiz_id' => Yii::t('app', 'Quiz'),
			'date_created' => Yii::t('app', 'Date Created'),
			'text' => Yii::t('app', 'Comment'),
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
		$criteria->compare('respondent_id',$this->respondent_id);
		$criteria->compare('quiz_id',$this->quiz_id);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}