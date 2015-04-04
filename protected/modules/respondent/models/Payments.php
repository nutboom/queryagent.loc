<?php

/**
 * This is the model class for table "{{respondents_payments}}".
 *
 * The followings are the available columns in table '{{respondents_payments}}':
 * @property integer $id
 * @property integer $respondent_id
 * @property string $datetime
 * @property double $money
 * @property string $state
 * @property string $type
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property Respondents $respondent
 */
class Payments extends CActiveRecord
{
        const STATE_EXPECT = 'expect';
        const STATE_REJECT = 'reject';
        const STATE_HELD = 'held';

	const TYPE_PHONE = 'phone';
	const TYPE_QIWI = 'qiwi';

	public static $LIMIT_MONEY_DAY = 200;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RespondentPayments the static model class
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
		return Yii::app()->getModule('respondent')->tableRespondentsPayments;
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
			array('money', 'numerical'),
			array('state', 'length', 'max'=>6),
			array('type', 'length', 'max'=>5),
			array('comment', 'length', 'max'=>255),
			array('datetime', 'safe'),
                        array('comment, money','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, respondent_id, datetime, money, state, type, comment', 'safe', 'on'=>'search'),
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
			'respondent' => array(self::BELONGS_TO, 'Respondents', 'respondent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'respondent_id' => RespondentModule::t('Respondent'),
			'datetime' => RespondentModule::t('Datetime payments'),
			'money' => RespondentModule::t('Money payments'),
			'state' => RespondentModule::t('State payments'),
			'type' => RespondentModule::t('Type payments'),
			'comment' => RespondentModule::t('Comment payments'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'PaymentsState' => array(
				self::STATE_EXPECT => RespondentModule::t('Expect state'),
				self::STATE_REJECT => RespondentModule::t('Reject state'),
				self::STATE_HELD => RespondentModule::t('Held state'),
			),
			'PaymentsType' => array(
				self::TYPE_PHONE => RespondentModule::t('Phone'),
				self::TYPE_QIWI => RespondentModule::t('Qiwi'),
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
		$criteria->compare('respondent_id',$this->respondent_id);
		$criteria->compare('datetime',$this->datetime,true);
		$criteria->compare('money',$this->money);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('comment',$this->comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}