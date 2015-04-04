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
class ExtendLicense extends CActiveRecord
{
	public $months;

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
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	protected function beforeValidate() {
		if(parent::beforeValidate()) {
			// собираем предварительные данные
			$user		=	User::model()->findByPk(Yii::app()->user->id);
			$license	=	$user->license[0];
			$tariff		=	$license->limits;

			// вычисляем сколько это ему будет стоит
			$cost		=	$tariff->cost*$this->months;

			// осилит ли? ;)
			if ($cost > $user->balance) {
				$this->addError("", Yii::t('app', 'Enough money to renew license', array("{summ}" => ($cost - $user->balance), "{link}" => Yii::app()->createUrl("/user/finance/pay"))));
				return false;
			}

			// продливаем срок
			if ((int) $this->date_expirate) {
				$this->date_expirate	=	new CDbExpression('DATE_ADD(`date_expirate`, INTERVAL '.$this->months.' MONTH)');
			}
			else {
				$this->date_open		=	new CDbExpression('NOW()');
				$this->date_expirate	=	new CDbExpression('DATE_ADD(NOW(), INTERVAL '.$this->months.' MONTH)');
			}

   			return true;
		}
		else {
			return false;
        }
	}

	protected function afterSave() {
		// собираем предварительные данные
		$user		=	User::model()->findByPk(Yii::app()->user->id);
		$license	=	$user->license[0];
		$tariff		=	$license->limits;

		// вычисляем сколько это ему будет стоит
		$cost		=	$tariff->cost*$this->months;

		// снимаем деньги с баланса
		$user->balance	=	$user->balance - $cost;
		$user->save();

		Mailtpl::send("pay_license", $user->email);

		parent::afterSave();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user, tariff, active', 'required', 'on' => 'insert'),
			array('date_expirate, date_open', 'safe'),
			array('tariff, months', 'numerical', 'integerOnly'=>true),
			array('id, user, active, tariff, date_expirate, date_open', 'safe', 'on'=>'search'),
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
			'months' => Yii::t('app', 'Num month for payment'),
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