<?php

/**
 * This is the model class for table "{{licenses}}".
 *
 * The followings are the available columns in table '{{licenses}}':
 * @property string $id
 * @property string $user
 * @property string $active
 * @property integer $tariff
 * @property string $date_expirate
 * @property string $date_open
 */
class Licenses extends CActiveRecord
{
	const ACTIVE = "yes";
	const DEACTIVE = "no";

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
		return '{{licenses}}';
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user, tariff, active', 'required', 'on' => 'insert'),
			array('date_open', 'date', 'format' => 'yyyy-MM-dd'),
			array('date_expirate', 'safe'),
			array('tariff', 'numerical', 'integerOnly'=>true),
			array('id, user, active, tariff, date_expirate, date_open', 'safe', 'on'=>'search'),
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
			'limits' => array(self::BELONGS_TO, 'Tariffs', 'tariff'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user' => 'User',
			'active' => 'Active',
			'tariff' => 'Tariff',
			'date_expirate' => 'Date Expirate',
			'date_open' => 'Date Open',
		);
	}

	public static function aliasActive($type) {
		$_items = array(
			'no' => Yii::t('app','No'),
			'yes' => Yii::t('app','Yes'),
		);

		return $_items[$type];
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
		$criteria->compare('active',$this->active,true);
		$criteria->compare('tariff',$this->tariff);
		$criteria->compare('date_expirate',$this->date_expirate,true);
		$criteria->compare('date_open',$this->date_open,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}