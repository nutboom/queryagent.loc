<?php
class Templates extends CActiveRecord {
    const TYPE_GENERAL = 'quiz';
	const TYPE_MISSION = 'mission';

    const DELETED = '1';
    const NO_DELETED = '0';
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Quiz the static model class
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
		return '{{templates}}';
	}

 
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, manager_id, type', 'required'),
            array('manager_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>150),
			array('type', 'length', 'max'=>7),
			array('title, manager_id, type', 'safe', 'on'=>'search'),
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
			'manager' => array(self::BELONGS_TO, 'User', 'manager_id'),
			'groupsQuestions' => array(self::HAS_MANY, 'TemplatesGroupQuestions', 'template_id','order'=>'groupsQuestions.orderby'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'title' => Yii::t('app', 'Title'),
			'manager_id' => Yii::t('app', 'Manager'),
			'type' => Yii::t('app', 'Type'),
		);
	}




        public static function itemAlias($type,$code=NULL) {
            $_items = array(
                'QuizType' => array(
                    self::TYPE_GENERAL => Yii::t('app', 'Quiz'),
                    self::TYPE_MISSION => Yii::t('app', 'Mission'),
                ),
            );

            if (isset($code))
                return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
            else
                return isset($_items[$type]) ? $_items[$type] : false;
	}

        /**
         * Количество вопросов опроса
         */
        public function countQuestions() {
            $questionsArr = array();

            foreach ($this->groupsQuestions as $g => $group) {
                foreach ($group->questions as $q => $question) {
                    $questionsArr[$question['id']] = $question;
                }
            }

            return count($questionsArr);
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

               /* if (!Yii::app()->getModule('user')->isAdmin()){
                    $clients = Yii::app()->user->getClients(Yii::app()->user->id);
                    $criteria->addInCondition('client_id', array_keys($clients));
                }*/

		$criteria->compare('title',$this->title,true);
		$criteria->compare('manager_id',$this->manager_id);
		$criteria->compare('type',$this->type,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}