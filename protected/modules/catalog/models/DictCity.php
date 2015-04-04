<?php

/**
 * This is the model class for table "{{dict_city}}".
 *
 * The followings are the available columns in table '{{dict_city}}':
 * @property integer $dict_city_id
 * @property string $title
 * @property integer $country_id
 *
 * The followings are the available model relations:
 * @property DictCountry $country
 */
class DictCity extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictCity the static model class
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
		return '{{dict_city}}';
	}

	protected function beforeSave() {
		if(parent::beforeSave()) {
			$this->last_update = new CDbExpression('NOW()');

			return true;
		}
		else {
			return false;
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
                        array('title, country_id', 'required'),
			array('country_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>150),
                        array('title','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('dict_city_id, title, country_id', 'safe', 'on'=>'search'),
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
			'country' => array(self::BELONGS_TO, 'DictCountry', 'country_id'),
                        'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_city(target_audience_id, city_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dict_city_id' => 'Dict City',
			'title' => Yii::t('app', 'Title'),
			'country_id' => Yii::t('app', 'Country'),
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

		$criteria->compare('dict_city_id',$this->dict_city_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('country_id',$this->country_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}