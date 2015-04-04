<?php

/**
 * This is the model class for table "{{clients}}".
 *
 * The followings are the available columns in table '{{clients}}':
 * @property integer $clients_id
 * @property string $name
 */
class Client extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Client the static model class
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
		return '{{clients}}';
	}

	/*
		This method is invoked before validation a record.
	*/
	protected function beforeValidate() {
		if(parent::beforeValidate()){
			if($this->isNewRecord) {
				// check num clients in user tariff
				//, 'order'=>'license.id DESC', 'limit' => 1, 'condition'=>"license.active='yes'",
                if (!User::model()->findByPk(Yii::app()->user->id)->subfor) {
					$count	=	count(User::model()->findByPk(Yii::app()->user->id)->client);
					$user	=	User::model()->findByPk(Yii::app()->user->id)->license[0];
					$max	=	$user->limits->limit_companys;

					if ($count >= $max && $max !== "0" && $user->active == "yes") {
						$this->addError("", UserModule::t('The maximum number of clients', array("{max}" => $max)));
						return false;
					}
				}
			}

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
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			array('name','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
                    'webusers'  =>  array(self::MANY_MANY, 'User', 'tbl_users_clients(clients_id, users_id)')
		);
	}

        public function behaviors() {
            return array(
                'withRelated'=>array(
                    'class'=>'ext.withRelated.WithRelatedBehavior',
                ),
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Clients',
			'name' => Yii::t('app', 'Name'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}