<?php

/**
 * This is the model class for table "{{dict_job_position}}".
 *
 * The followings are the available columns in table '{{dict_job_position}}':
 * @property integer $dict_job_position_id
 * @property string $title
 */
class DictJobPosition extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictJobPosition the static model class
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
		return '{{dict_job_position}}';
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
			array('dict_job_position_id, title', 'safe', 'on'=>'search'),
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
                    'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_job_position(target_audience_id, job_position_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dict_job_position_id' => 'Dict Job Position',
			'title' => Yii::t('app', 'Title'),
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

		$criteria->compare('dict_job_position_id',$this->dict_job_position_id);
		$criteria->compare('title',$this->title,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}