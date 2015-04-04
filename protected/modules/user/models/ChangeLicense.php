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
class ChangeLicense extends CActiveRecord {
	public $cost;

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
			$license	=	Licenses::model()->findByPk($user->license[0]->id);
			$tariff		=	$license->limits;
			$newTariff	=	Tariffs::model()->findByPk($this->tariff);

			$connection	=	Yii::app()->db;
			$command	=	$connection->createCommand("SELECT TO_DAYS(`date_expirate`) - TO_DAYS(NOW()) FROM `tbl_licenses` WHERE `id` = '".$license->id."'");
			$days		=	current($command->query()->read());
			$residue	=	($days/30)*$tariff->cost;

			$this->cost	=	$newTariff->cost*$newTariff->minimum - $residue;

			// осилит ли? ;)
			if ($this->cost > $user->balance) {
				$this->addError("", Yii::t('app', 'Enough money to change tariff', array("{summ}" => ($this->cost - $user->balance), "{link}" => Yii::app()->createUrl("/user/finance/pay"))));
				return false;
			}

			$this->user				=	Yii::app()->user->id;
			$this->date_open		=	Date("Y-m-d");
			$this->active			=	"yes";
			$this->date_expirate	=	new CDbExpression('DATE_ADD(NOW(), INTERVAL '.$newTariff->minimum.' MONTH)');

			$license->active		=	"no";
			$license->date_expirate	=	Date("Y-m-d");
			$license->save();

   			return true;
		}
		else {
			return false;
        }
	}

	protected function afterSave() {
		// собираем предварительные данные
		$user		=	User::model()->findByPk(Yii::app()->user->id);

		// снимаем деньги с баланса
		$user->balance	=	$user->balance - $this->cost;
		$user->save();

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
			array('date_open', 'date', 'format' => 'yyyy-MM-dd'),
			array('date_expirate', 'safe'),
			array('tariff', 'numerical', 'integerOnly'=>true),
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
			'tariff' => Yii::t('app', 'New tariff plan'),
			'date_expirate' => 'Date Expirate',
			'date_open' => 'Date Open'
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