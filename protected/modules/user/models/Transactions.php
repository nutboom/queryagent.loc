<?php
/**
 * This is the model class for table "{{transactions}}".
 *
 * The followings are the available columns in table '{{transactions}}':
 */
class Transactions extends CActiveRecord {

	const STATUS_CREATED = 'created';
	const STATUS_PAYED = 'payed';
	const STATUS_REFUSED = 'refused';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Licenses the static model class
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
		return '{{transactions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user, summ, date_open, status', 'required', 'on' => 'insert'),
			array('status, date_close', 'required', 'on' => 'update'),
			array('summ', 'numerical', 'min'=>Yii::app()->params['minimalSummPayment'], 'tooSmall'=>Yii::t('app','Minimal summ of pay - {summ} rub', array('{summ}'=>Yii::app()->params['minimalSummPayment']))),
			array('date_open', 'date', 'format' => 'yyyy-MM-dd hh:mm:ss'),
			array('date_close', 'safe'),
			array('id, user, summ, status, date_open, date_close', 'safe', 'on'=>'search'),
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
			'manager' => array(self::BELONGS_TO, 'User', 'user'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user' => Yii::t('app', 'User'),
			'summ' => Yii::t('app', 'Summ'),
			'status' => Yii::t('app', 'Status'),
			'date_open' => Yii::t('app', 'Date Open'),
			'date_close' => Yii::t('app', 'Date Close'),
		);
	}

	public static function itemAlias($type, $all=false) {
		$_items = array(
			self::STATUS_CREATED => Yii::t('app', 'Status created'),
			self::STATUS_PAYED => Yii::t('app', 'Status payed'),
			self::STATUS_REFUSED => Yii::t('app', 'Status refused'),
		);

		if ($all) {
        	return $_items;
		}
		else {
			return isset($_items[$type]) ? $_items[$type] : false;
		}
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
		$criteria->compare('user',$this->user,true);
		$criteria->compare('summ',$this->summ,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('date_open',$this->date_open,true);
		$criteria->compare('date_close',$this->date_close,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}