<?php

/**
 * This is the model class for table "{{smstpl}}".
 *
 * The followings are the available columns in table '{{smstpl}}':
 * @property integer $dict_scope_id
 * @property string $title
 * @property integer $is_job
 */
class SmsTpl extends CActiveRecord {

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
		return '{{smstpl}}';
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('template, type, user', 'required'),
			array('user', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('template, type, user', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user' => 'user',
			'type' => 'type',
			'template' => 'template',
		);
	}

    public static function getTpl($type) {
    	$templates = array(
    		'smsnewgroup' => Yii::t('app','Download application').': https://itunes.apple.com/ru/app/query-agent/id710379887?ls=1&mt=8',
    		'emailnewgroup' => Yii::t('app','Download application'),
    		'' => '',
    	);

		$template = SmsTpl::model()->find("user = :user AND type = :type", array(":user" => Yii::app()->user->id, ":type" => $type));
		if ($template) {
			return Quiz::bb($template->template);
		}
		else {
			return $templates[$type];
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

		$criteria->compare('user',$this->user);
		$criteria->compare('type',$this->type);
		$criteria->compare('template',$this->template);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}