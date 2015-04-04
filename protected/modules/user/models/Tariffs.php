<?php

/**
 * This is the model class for table "{{tariffs}}".
 *
 * The followings are the available columns in table '{{tariffs}}':
 * @property integer $id
 * @property string $name
 * @property string $cost
 * @property integer $minimum
 * @property integer $limit_users
 * @property integer $limit_respondents
 * @property integer $limit_groups
 * @property integer $limit_quizs
 * @property integer $limit_companys
 * @property string $limit_templates
 * @property string $limit_brand_quiz
 * @property string $limit_brand_site
 */
class Tariffs extends CActiveRecord
{
	private $_modelReg;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tariffs the static model class
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
		return '{{tariffs}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, cost, free_month, limit_users, limit_respondents, limit_groups, limit_quizs, limit_companys, limit_templates, limit_brand_quiz, limit_brand_site', 'required'),
			array('minimum, free_month, limit_users, limit_respondents, limit_groups, limit_quizs, limit_companys', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>10),
			array('cost', 'length', 'max'=>6),
			array('limit_templates, limit_brand_quiz, limit_brand_site', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, cost, free_month, minimum, limit_users, limit_respondents, limit_groups, limit_quizs, limit_companys, limit_templates, limit_brand_quiz, limit_brand_site', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название',
			'cost' => 'Стоимость',
			'free_month' => 'Количество бесплатных месяцев',
			'minimum' => 'Минимальный заказ в месяцах',
			'limit_users' => 'Суб. пользователей',
			'limit_respondents' => 'Количество респондентов',
			'limit_groups' => 'Количество групп',
			'limit_quizs' => 'Количество опросов',
			'limit_companys' => 'Количество компаний',
			'limit_templates' => 'Доступность шаблонов опросов',
			'limit_brand_quiz' => 'Брендирование опросов (бренд заказчика)',
			'limit_brand_site' => 'Брендирование админки',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('free_month',$this->free_month,true);
		$criteria->compare('minimum',$this->minimum);
		$criteria->compare('limit_users',$this->limit_users);
		$criteria->compare('limit_respondents',$this->limit_respondents);
		$criteria->compare('limit_groups',$this->limit_groups);
		$criteria->compare('limit_quizs',$this->limit_quizs);
		$criteria->compare('limit_companys',$this->limit_companys);
		$criteria->compare('limit_templates',$this->limit_templates,true);
		$criteria->compare('limit_brand_quiz',$this->limit_brand_quiz,true);
		$criteria->compare('limit_brand_site',$this->limit_brand_site,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function monthTariffs($tariff) {
		$tariff = self::model()->findByPk($tariff);
		$extension_months	=	Explode(",", $tariff->extension_months);
		$months	=	Array();
		foreach($extension_months as $month) {
			if ($month) {
				$months[$month] = $month;
			}
		}

		return $months;
	}

	public static function selectTariff($tariff) {
		$tariffs = self::model()->findAll("id > :tariff", array(":tariff" => $tariff));
		$return	=	Array();
		foreach($tariffs as $tariff) $return[$tariff->id] = $tariff->name;

		return $return;
	}
}