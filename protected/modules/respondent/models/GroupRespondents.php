<?php

/**
 * This is the model class for table "{{group_respondents}}".
 *
 * The followings are the available columns in table '{{group_respondents}}':
 * @property integer $id
 * @property string $title
 * @property integer $manager_id
 * @property string $created_at
 * @property string $updated_at
 */
class GroupRespondents extends CActiveRecord
{
        public $textarea;
    public $sender_sms;
    public $sender_email;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GroupRespondents the static model class
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
		return Yii::app()->getModule('respondent')->tableRespondentsGroup;
	}

        /**
	 * This method is invoked before validation a record.
         */
        protected function beforeValidate() {
            if(parent::beforeValidate()){
                $result = $this->parsingNewRespondents();

                if(!$result && !is_array($result)) {
                    $this->addError ('textarea', RespondentModule::t('Syntax error'));
                    return false;
                }
                else if($result){
					if(count($this->respondents) > 0) {
						$this->respondents = array_merge($this->respondents, $result);
					}
 					else {
 						$this->respondents = $result;
 					}
				}

                // cost of sms sending
                $cost = ceil(count($result) * Yii::app()->params['sendSmsCost']);
                if ($cost > User::model()->findByPk(Yii::app()->user->id)->balance && $this->sender_sms) {
                    $this->addError ('textarea', RespondentModule::t('Insufficient funds on the balance sheet for the SMS-mailing. Discard SMS-mailing or refill balance'));
                }

				if($this->isNewRecord) {
					// check num groups in user tariff
					if (!User::model()->findByPk(Yii::app()->user->id)->subfor) {
						$count	=	self::model()->count("manager_id = :id", array(":id" => Yii::app()->user->id));
						$user	=	User::model()->findByPk(Yii::app()->user->id)->license[0];
						$max	=	$user->limits->limit_groups;

						if ($count >= $max && $max !== "0" && $user->active == "yes") {
							$this->addError("", RespondentModule::t('The maximum number of groups', array("{max}" => $max)));
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
                if($this->isNewRecord) {
                    $this->manager_id = Yii::app()->user->id;
                    $this->created_at = date('Y-m-d H:i:s');
                }

                $this->updated_at = date('Y-m-d H:i:s');

                return true;
            } else
                return false;
        }

        /**
         * This is invoked after the record is saved.
         */
        protected function afterSave()
        {
                parent::afterSave();
                $respondents = $this->respondents;
                $costSms = 0;
                foreach ($respondents as $respondent) {
                    if(!$respondent->phone_is_confirmed) {
                        if($respondent->isNewRecord ) {
                           if ($respondent->phone_number && $this->sender_sms) {
                                $text = SmsTpl::getTpl("smsnewgroup");
 
                                Utils::send_sms($respondent->phone_number, $text);
                                $costSms += Yii::app()->params['sendSmsCost'];                      
                            }
                            if($respondent->email_actual && $this->sender_email) {
                                $text = SmsTpl::getTpl("emailnewgroup");
 
                                Mailtpl::send("add_in_group", $respondent->email_actual, array("{offer_name}"=>Yii::app()->user->last_name.' '.Yii::app()->user->first_name, "{offer_mail}"=>Yii::app()->user->email));
                            }
                        }
                    }
                }

                # remove money from the balance
                $costSms = ceil($costSms);
                if ($costSms && $this->sender_sms) {
                    $user = User::model()->findByPk(Yii::app()->user->id);
                    $user->balance -= $costSms;
                    $user->save();
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
            array('sender_sms, sender_email', 'numerical', 'integerOnly'=>true),

            array('title', 'required'),
			array('manager_id, client_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title, manager_id, client_id', 'safe', 'on'=>'search'),
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
                    'manager'=> array(self::BELONGS_TO, 'User', 'manager_id'),
                    'client'=> array(self::BELONGS_TO, 'Client', 'client_id'),
                    'respondents' => array(self::MANY_MANY, 'Respondent', 'tbl_link_users_group_respondents(group_respondents_id, respondents_id)','index'=>'id','order'=>'respondents.id'),
                    'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_group_respondents(group_id, target_audience_id)'),
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
			'title' => Yii::t('app', 'Title'),
			'manager_id' => Yii::t('app', 'Manager'),
			'client_id' => Yii::t('app', 'Clients'),
			'clientName' => Yii::t('app', 'Client Name'),
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('manager_id',$this->manager_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
         * Delete from table connect with Respondent
         */
        public function deleteConnectionWithRespondents($arrayIds){
            $respondentIds = implode(',', $arrayIds);
            $group_id = $this->id;
            $sql = "
            	DELETE gr1.* FROM tbl_link_users_group_respondents gr1
                INNER JOIN (
                	SELECT r.id FROM tbl_respondents r
                	LEFT JOIN tbl_link_users_group_respondents gr ON gr.respondents_id=r.id
					WHERE gr.group_respondents_id=:group AND r.id NOT IN (:ids) AND r.phone_is_confirmed=1
				) fg
				ON gr1.respondents_id=fg.id
				WHERE group_respondents_id=:group
			";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":group", $group_id);
            $command->bindParam(":ids", $respondentIds);
            $command->execute();
            //Yii::app()->db->createCommand('DELETE FROM `tbl_users_clients` WHERE users_id=:user')->bindParam(":user",$model->id)->execute();
        }

        protected function parsingNewRespondents() {
            $arrayNewRespondents = array();
            if(trim($this->textarea)){
                $respondents = explode(';', $this->textarea);
                if(count($respondents)){
                    foreach ($respondents as $r => $respondent) {
                        $name = '';
                        $phone = '';
                        $mail = '';

                        if(trim($respondent)) {
                            list($name, $phone, $mail) = explode(',', $respondent);

                            $phone = preg_replace("/[^0-9]+/i", "", $phone);
                            $phone = preg_replace("/^8([0-9]+)/i", "7$1", $phone);

                            if (empty($phone) && empty($mail)) {
                                continue;
                            }

                            if (!empty($phone)) {
                                $model = Respondent::model()->find("phone_number LIKE :phone", array(':phone'=>$phone));
                            }
                            else {
                                $model = Respondent::model()->find("email_actual LIKE :mail", array(':mail'=>$mail));
                            }

                            if (!$model) {
                                $model = new Respondent;
                                $model->payable = 0;

                                if ($phone) {
                                    $model->phone_number = $phone;
                                }

                                if ($name) {
                                    $parseName = explode(' ', trim($name));
                                    if (count($parseName) == 2) {
                                        $model->last_name = trim($parseName[0]);
                                        $model->first_name = trim($parseName[1]);
                                    }
                                    else {
                                        $model->last_name = trim($name);   
                                    }
                                }

                                if ($mail) {
                                    $model->email_new = $mail;
                                    $model->email_actual = $mail;
                                }

                                if (!$model->validate()) {
                                    return false;    
                                }  
                            }

                            $arrayNewRespondents[] = $model;
                        }
                    }
                }
            }
            return $arrayNewRespondents;
        }

        /*
         *
         */
        public function getClientName(){
            return $this->client->name;
        }
}