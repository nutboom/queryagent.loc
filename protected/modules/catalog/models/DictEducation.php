<?php

/**
 * This is the model class for table "{{dict_education}}".
 *
 * The followings are the available columns in table '{{dict_education}}':
 * @property integer $dict_education_id
 * @property string $title
 */
class DictEducation extends CActiveRecord
{
        public $id;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictEducation the static model class
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
		return '{{dict_education}}';
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
			array('title', 'required'),
                        array('title', 'length', 'max'=>150),
                        array('title','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('dict_education_id, title', 'safe', 'on'=>'search'),
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
                    'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_education_target_audience(target_audience_id, education_id)')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dict_education_id' => 'Dict Education',
			'title' => Yii::t('app', 'Title'),
			'id' => Yii::t('app', 'ID'),
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

		$criteria->compare('dict_education_id',$this->dict_education_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('id',$this->id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}