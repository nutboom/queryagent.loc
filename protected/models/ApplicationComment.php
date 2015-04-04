<?php

/**
 * This is the model class for table "{{application_comments}}".
 *
 * The followings are the available columns in table '{{application_comments}}':
 * @property integer $id
 * @property integer $application_id
 * @property string $date_created
 * @property string $state
 * @property string $role
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Applications $application
 */
class ApplicationComment extends CActiveRecord
{
	const ROLE_ADMIN = 'admin';
	const ROLE_CLIENT = 'client';
	const ROLE_RESPONDENT = 'respondent';

        /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ApplicationComment the static model class
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
		return '{{application_comments}}';
	}

        /**
	 * This method is invoked before saving a record (after validation, if any).
	 * The default implementation raises the {@link onBeforeSave} event.
	 * You may override this method to do any preparation work for record saving.
	 * Use {@link isNewRecord} to determine whether the saving is
	 * for inserting or updating record.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the saving should be executed. Defaults to true.
	 */
        protected function beforeSave()
        {
            if(parent::beforeSave())
            {
                if($this->isNewRecord)
                    $this->date_created = date('Y-m-d H:i:s');
                return true;
            }
            else
                return false;
        }

        /**
	 * This method is invoked after saving a record (after validation, if any).
	 */
    protected function afterSave() {
        parent::afterSave();
        $application = Application::model()->findByPk($this->application_id);
        
        if ($this->state == Application::STATE_APPEAL) {
        	$application->is_appeal = 1;
        }
        else {
        	if ($application->is_appeal && $this->state == Application::STATE_CLOSE) {
        		$application->is_appeal = 0;
        	}

        	$application->state = $this->state;
        }
        
	    $application->save();
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('application_id', 'numerical', 'integerOnly'=>true),
			array('state', 'length', 'max'=>6),
			array('role', 'length', 'max'=>10),
			array('date_created, text', 'safe'),
                        array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, application_id, date_created, state, role, text', 'safe', 'on'=>'search'),
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
			'application' => array(self::BELONGS_TO, 'Applications', 'application_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'application_id' => Yii::t('app','Application'),
			'date_created' => Yii::t('app','Date Created'),
			'state' => Yii::t('app','State'),
			'role' => Yii::t('app','Role'),
			'text' => Yii::t('app','Comment'),
		);
	}

        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'RoleSender' => array(
                    self::ROLE_ADMIN => Yii::app()->getModule('user')->t('Superuser'),
                    self::ROLE_CLIENT => Yii::app()->getModule('user')->t('Client'),
                    self::ROLE_RESPONDENT => Yii::app()->getModule('respondent')->t('Respondent'),
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

		$criteria->compare('id',$this->id);
		$criteria->compare('application_id',$this->application_id);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}