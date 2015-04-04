<?php

class m121210_113618_create_respondent extends CDbMigration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
        private $_model;
	public function safeUp()
	{
            if (!Yii::app()->getModule('respondent')) {
                echo "\n\nAdd to console.php :\n"
                     ."'modules'=>array(\n"
                     ."...\n"
                     ."    'respondent'=>array(\n"
                     ."        ... # copy settings from main config\n"
                     ."    ),\n"
                     ."...\n"
                     ."),\n"
                     ."\n";
                return false;
            }
            Yii::import('respondent.models.Respondent');

            $this->createTable(Yii::app()->getModule('respondent')->tableRespondentsStatuses, array(
                'id'    => 'pk',
                'title'             => 'VARCHAR(150)',
                'karma'             => 'FLOAT',
                'multiplicator'     => 'FLOAT',
            ), $this->MySqlOptions);

            $this->createTable(Yii::app()->getModule('respondent')->tableRespondents, array(
                "id" => "pk",
                "first_name" => "varchar(20) DEFAULT ''",
                "last_name" => "varchar(20) DEFAULT ''",

                "phone_number" => "varchar(20) DEFAULT ''",
                "phone_code" => "varchar(128) DEFAULT ''",
                "phone_code_expdate" => "varchar(20) DEFAULT ''",
                "phone_is_confirmed" => "int(1) DEFAULT 0",

                "email_actual" => "varchar(128) DEFAULT ''",
                "email_new" => "varchar(128) DEFAULT ''",
                "email_code" => "varchar(128) DEFAULT ''",
                "email_code_expdate" => "varchar(20) DEFAULT ''",

                "social_type" => "ENUM('none','vk.com','facebook.com','twitter.com','odnoklassniki.ru') DEFAULT 'none'",
                "social_key" => "varchar(128) DEFAULT ''",

                "password" => "varchar(128) DEFAULT ''",
                "password_code" => "varchar(10) DEFAULT ''",

                "avatar" => "varchar(128) DEFAULT ''",
                "sex" => "ENUM('none','male','female') DEFAULT 'none'",
                "marital_state" => "ENUM('none','single','married','divorced') DEFAULT 'none'",
                "birth_date" => "DATE",

                "position_id" => "int",
                "scope_id" => "int",
                "country_id" => "int",
                "city_id" => "int",

                "income" => "int DEFAULT 0",
                "money" => "float DEFAULT 0",
                "karma" => "float DEFAULT 0",
                "state_id" => "int",
            ), $this->MySqlOptions);

            $this->addForeignKey('respondent_state_id', Yii::app()->getModule('respondent')->tableRespondents, 'state_id', Yii::app()->getModule('respondent')->tableRespondentsStatuses, 'id', 'SET NULL', 'RESTRICT');

            $this->createTable(Yii::app()->getModule('respondent')->tableSessions, array(
                'session_id' => 'varchar(120)',
                'respondent_id' => 'INT(3)',
                'secret_id' => 'string',
                'datetime' => 'DATETIME',
                'PRIMARY KEY (`session_id`)'
            ), $this->MySqlOptions);
            $this->addForeignKey('respondent_session_id', Yii::app()->getModule('respondent')->tableSessions, 'respondent_id', Yii::app()->getModule('respondent')->tableRespondents, 'id', 'CASCADE', 'RESTRICT');

            $this->createTable(Yii::app()->getModule('respondent')->tableRespondentsPayments, array(
                "id" => "pk",
                "respondent_id" => "int DEFAULT 0",
                "datetime" => "DATETIME",
                "money" => "FLOAT DEFAULT 0",
                "state" => "ENUM('expect','reject','held') DEFAULT 'expect'",
                "type" => "ENUM('phone','qiwi')",
                "comment" => "varchar(255) DEFAULT 0",
            ), $this->MySqlOptions);
            $this->addForeignKey('respondent_payment_id', Yii::app()->getModule('respondent')->tableRespondentsPayments, 'respondent_id', Yii::app()->getModule('respondent')->tableRespondents, 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
            $this->dropTable(Yii::app()->getModule('respondent')->tableRespondentsStatuses);
            $this->dropTable(Yii::app()->getModule('respondent')->tableRespondents);
            $this->dropTable(Yii::app()->getModule('respondent')->tableSessions);
            $this->dropTable(Yii::app()->getModule('respondent')->tableRespondentsPayments);
	}
}