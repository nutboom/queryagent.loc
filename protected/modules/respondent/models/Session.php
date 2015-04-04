<?php

/**
 * This is the model class for table "{{sessions}}".
 *
 * The followings are the available columns in table '{{sessions}}':
 * @property integer $respondent_id
 * @property string $session_id
 * @property string $secret_id
 * @property string $datetime
 *
 * The followings are the available model relations:
 * @property Respondents $respondent
 */
class Session extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Session the static model class
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
		return Yii::app()->getModule('respondent')->tableSessions;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('respondent_id', 'numerical', 'integerOnly'=>true),
			array('session_id, secret_id, device_token', 'length', 'max'=>120),
			array('datetime', 'safe'),
			array('device_type', 'safe'),
			array('secret_id, device_token','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('respondent_id, session_id, secret_id, datetime, device_token, device_type', 'safe', 'on'=>'search'),
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
			'respondent' => array(self::BELONGS_TO, 'Respondent', 'respondent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'respondent_id' => RespondentModule::t('Respondent'),
			'session_id' => RespondentModule::t('Session'),
			'secret_id' => RespondentModule::t('Secret'),
			'datetime' => RespondentModule::t('Datetime session'),
			'device_token' => RespondentModule::t('Device token'),
			'device_type' => RespondentModule::t('Device type'),
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

		$criteria->compare('respondent_id',$this->respondent_id);
		$criteria->compare('session_id',$this->session_id,true);
		$criteria->compare('secret_id',$this->secret_id,true);
		$criteria->compare('datetime',$this->datetime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}