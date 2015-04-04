<?php

class User extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
	const STATUS_BANNED=-1;

	//TODO: Delete for next version (backward compatibility)
	const STATUS_BANED=-1;

	/**
	 * The followings are the available columns in table 'users':
	 * @var integer $id
	 * @var string $subfor
	 * @var string $avatar
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $activkey
	 * @var integer $createtime
	 * @var integer $lastvisit
	 * @var integer $superuser
	 * @var integer $manager
     * @var integer $marketer
	 * @var integer $status
     * @var timestamp $create_at
     * @var timestamp $lastvisit_at
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
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
		return Yii::app()->getModule('user')->tableUsers;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
        if (get_class(Yii::app()) == 'CConsoleApplication' || (get_class(Yii::app()) != 'CConsoleApplication' && Yii::app()->getModule('user')->isAdmin())) {
            return array(
                array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
                array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
                array('email', 'email'),
                array('phone_number', 'length', 'max'=>20),
                array('balance', 'numerical'),
                array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
                array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
                array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
                array('status', 'in', 'range'=>array(self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANNED)),
                array('superuser', 'in', 'range'=>array(0,1)),
                array('manager', 'in', 'range'=>array(0,1)),
                array('marketer', 'in', 'range'=>array(0,1)),
                array('create_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
                array('lastvisit_at', 'default', 'value' => '0000-00-00 00:00:00', 'setOnEmpty' => true, 'on' => 'insert'),
                array('username, email, superuser, manager, status', 'required'),
                array('superuser, manager, status, marketer', 'numerical', 'integerOnly'=>true),
                array('id, username, password, subfor, avatar, phone_number, balance, email, activkey, create_at, lastvisit_at, superuser, manager, marketer, status', 'safe', 'on'=>'search'),
                array('npassword', 'safe', 'on'=>'update'),
            );
        }
        else if (Yii::app()->user->id == $this->id) {
            return array(
                array('avatar', 'file', 'types'=> 'jpg, gif, png','allowEmpty'=>true,'safe'=>false),
                array('username, email, phone_number', 'required'),
                array('password', 'safe'),
                array('phone_number', 'length', 'max'=>20),
                array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
                array('email', 'email'),
                array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
                array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
                array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
            );
        }
        else {
            return array('username, email, phone_number', 'required');
        }

		/*// NOTE: you should only define rules for those attributes that
		// will receive user inputs.CConsoleApplication
		return ((get_class(Yii::app())=='CConsoleApplication' || (get_class(Yii::app())!='CConsoleApplication' && Yii::app()->getModule('user')->isAdmin()))?array(
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			array('email', 'email'),
            array('phone_number', 'length', 'max'=>20),
			array('balance', 'numerical'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('status', 'in', 'range'=>array(self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANNED)),
			array('superuser', 'in', 'range'=>array(0,1)),
			array('manager', 'in', 'range'=>array(0,1)),
			array('marketer', 'in', 'range'=>array(0,1)),
            array('create_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
            array('lastvisit_at', 'default', 'value' => '0000-00-00 00:00:00', 'setOnEmpty' => true, 'on' => 'insert'),
			array('username, email, superuser, manager, status', 'required'),
			array('superuser, manager, status, marketer', 'numerical', 'integerOnly'=>true),
			array('id, username, password, subfor, avatar, phone_number, balance, email, activkey, create_at, lastvisit_at, superuser, manager, marketer, status', 'safe', 'on'=>'search'),
			array('npassword', 'safe', 'on'=>'update'),
		):((Yii::app()->user->id==$this->id)?array(
			array('avatar', 'file', 'types'=>'jpg, gif, png','allowEmpty'=>true,'safe'=>false),
			array('username, email, phone_number', 'required'),
			array('password', 'safe'),
            array('phone_number', 'length', 'max'=>20),
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
		):array(
			array('username, email, phone_number', 'required'),
		)));*/
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        $relations = Yii::app()->getModule('user')->relations;
        if (!isset($relations['profile']))
            $relations['profile'] = array(self::HAS_ONE, 'Profile', 'user_id');
        if (!isset($relations['client']))
            $relations['client'] = array(self::MANY_MANY, 'Client', 'tbl_users_clients(users_id, clients_id)','index'=>'id');

        $relations['license'] = array(self::HAS_MANY, 'Licenses', 'user', 'order'=>'license.id DESC');

        $relations['branding'] = array(self::HAS_ONE, 'Branding', 'user');

        $relations['quizs'] = array(self::HAS_MANY, 'Quiz', 'manager_id', 'condition'=>"quizs.type='quiz'");
        $relations['missions'] = array(self::HAS_MANY, 'Quiz', 'manager_id', 'condition'=>"missions.type='mission'");

        return $relations;
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
			'id' => UserModule::t("Id"),
			'subfor' => UserModule::t("Subfor"),
			'avatar' => UserModule::t("avatar"),
            'phone_number' => Yii::t('app', 'Phone number'),
			'balance' => Yii::t('app', "Balance"),
			'username'=>UserModule::t("username"),
			'password'=>UserModule::t("password"),
			'npassword'=>UserModule::t("npassword"),
			'verifyPassword'=>UserModule::t("Retype Password"),
			'email'=>UserModule::t("E-mail"),
			'verifyCode'=>UserModule::t("Verification Code"),
			'activkey' => UserModule::t("activation key"),
			'createtime' => UserModule::t("Registration date"),
			'create_at' => UserModule::t("Registration date"),

			'lastvisit_at' => UserModule::t("Last visit"),
			'superuser' => UserModule::t("Superuser"),
			'manager' => UserModule::t("Manager"),
			'marketer' => UserModule::t("Marketer"),
			'client' => UserModule::t("Client"),
			'status' => UserModule::t("Status"),
		);
	}

	public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactive'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANNED,
            ),
            'superuser'=>array(
                'condition'=>'superuser=1',
            ),
            'manager'=>array(
                'condition'=>'manager=1',
            ),
            'marketer'=>array(
                'condition'=>'marketer=1',
            ),
            'viewClients'=>array(
                'condition'=>'marketer=0 AND manager=0 AND superuser=0',
            ),
            'notsafe'=>array(
            	'select' => 'id, username, password, email, activkey, create_at, lastvisit_at, superuser, manager, marketer, status',
            ),
        );
    }

	public function defaultScope()
        {
            return CMap::mergeArray(Yii::app()->getModule('user')->defaultScope,array(
                'alias'=>'user',
                'select' => 'user.id, user.username, user.email, user.create_at, user.lastvisit_at, user.subfor, user.balance, user.avatar, user.phone_number, user.superuser, user.manager, user.marketer, user.status',
            ));
        }

	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'UserStatus' => array(
				self::STATUS_NOACTIVE => UserModule::t('Not active'),
				self::STATUS_ACTIVE => UserModule::t('Active'),
				self::STATUS_BANNED => UserModule::t('Banned'),
			),
			'AdminStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
			'ManagerStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
			'MarketerStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
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
        $criteria->compare('subfor',$this->subfor);
        $criteria->compare('balance',$this->balance);
        $criteria->compare('avatar',$this->avatar);
        $criteria->compare('phone_number',$this->phone_number);
        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('activkey',$this->activkey);
        $criteria->compare('create_at',$this->create_at);
        $criteria->compare('lastvisit_at',$this->lastvisit_at);
        $criteria->compare('superuser',$this->superuser);
        $criteria->compare('manager',$this->manager);
        $criteria->compare('marketer',$this->marketer);
        $criteria->compare('status',$this->status);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        	'pagination'=>array(
				'pageSize'=>Yii::app()->getModule('user')->user_page_size,
			),
        ));
    }

    public function getCreatetime() {
        return strtotime($this->create_at);
    }

    public function setCreatetime($value) {
        $this->create_at=date('Y-m-d H:i:s',$value);
    }

    public function getLastvisit() {
        return strtotime($this->lastvisit_at);
    }

    public function setLastvisit($value) {
        $this->lastvisit_at=date('Y-m-d H:i:s',$value);
    }

    public function getNpassword() {
        return '';
    }

    public function setNpassword($password) {
        $password = trim($password);
        if (!empty($password)) {
             $this->password = $password;
        }
    }

    /**
     * Delete from table 'tbl_users_clients' connect with Client and User
     */
    public function deleteConnectionWithClient(){
        $user = $this->id;
        $table = 'tbl_users_clients';
        $sql = 'DELETE FROM '.$table.' WHERE users_id=:user';
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":user", $user);
        $command->execute();
        //Yii::app()->db->createCommand('DELETE FROM `tbl_users_clients` WHERE users_id=:user')->bindParam(":user",$model->id)->execute();
    }

    /**
     * Save clients the model
     */
    public function setClients($clientArr){
        if(!$this->superuser && !$this->manager && !$this->marketer)
            $clientArr = array($clientArr);

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $clientArr);
        $criteria->index = 'id';
        $clients = Client::model()->findAll($criteria);

        $this->client = $clients;
    }

    /*
     *
     */
    public function getRespondentsInGroups(){
        $criteria = new CDbCriteria();
        //$criteria->addCondition('phone_is_confirmed=:confirm');
        $criteria->with = array('groups');
        $criteria->addCondition('groups.manager_id=:manager');
        $criteria->params = array(':manager'=>$this->id/*, 'confirm'=>1*/);
        $criteria->index = 'id';
        $respondents = Respondent::model()->findAll($criteria);
        return $respondents;
    }
}