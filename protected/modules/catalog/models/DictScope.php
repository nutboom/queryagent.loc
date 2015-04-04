<?php

/**
 * This is the model class for table "{{dict_scope}}".
 *
 * The followings are the available columns in table '{{dict_scope}}':
 * @property integer $dict_scope_id
 * @property string $title
 * @property integer $is_job
 */
class DictScope extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictScope the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{dict_scope}}';
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
			array('is_job', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>150),
                        array('title','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('dict_scope_id, title, is_job', 'safe', 'on'=>'search'),
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
                    'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_scope(target_audience_id, scope_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dict_scope_id' => 'Dict Scope',
			'title' => Yii::t('app', 'Title'),
			'is_job' => Yii::t('app', 'Is Job'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'IsJob' => array(
                    '0' => Yii::t('app','No'),
                    '1' => Yii::t('app','Yes'),
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

		$criteria->compare('dict_scope_id',$this->dict_scope_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('is_job',$this->is_job);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}