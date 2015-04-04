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
class Mailtpl extends CActiveRecord
{
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
		return '{{mailtpl}}';
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, title, content', 'required'),
			array('name, title, content', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array( );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('app', 'Name of email template'),
			'title' => Yii::t('app', 'Title of email template'),
			'content' => Yii::t('app', 'Content of email template'),
		);
	}


	public static function send($name, $to, array $params = array()) {
		$template = '<html>
			<head></head>
			<body style="background: #e3e7e8;">
				<div style="background: #fff; width: 600px; margin: 0 auto; margin-top: 50px;">
					<img src="http://admin.queryagent.ru/images/mail/top.png">
					<div style="padding: 10px;">
						{content}
					</div>
					<img src="http://admin.queryagent.ru/images/mail/footer.png">
				</div>
			</body>
		</html>';

		$model = Mailtpl::model()->findByPk($name);

		$headers = "From: ".Yii::app()->params['adminEmail']."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Reply-To: ".Yii::app()->params['adminEmail']."\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

		$subject = '=?UTF-8?B?'.base64_encode($model->title).'?=';

		$content = $model->content;
		foreach ($params as $param => $value) {
			$param = ltrim($param, "{");
			$param = rtrim($param, "}");
			$content = str_ireplace("{".$param."}", $value, $content);
		}

		$connection=Yii::app()->db;
		$connection->createCommand("
			INSERT INTO `tbl_mail_cron` SET
				`mail`		=	'".$to."',
				`subject`	=	'".$subject."',
				`content`	=	'".str_replace("{content}", $content, $template)."',
				`headers`	=	'".$headers."'
		")->execute();

		#mail($to, $subject, str_replace("{content}", $content, $template), $headers);
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}