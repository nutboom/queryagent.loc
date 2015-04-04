<?php

class m131009_073810_create_table_group_respondents extends CDbMigration
{
        protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable(Yii::app()->getModule('respondent')->tableRespondentsGroup, array(
                'id'             => 'pk',
                'title'          => 'VARCHAR(150)',
                'manager_id'     => 'INT(3)',
                'client_id'     => 'INT(3)',
                'created_at'     => 'DATETIME',
                'updated_at'     => 'DATETIME',
            ), $this->MySqlOptions);
            $this->addForeignKey('menager_group_respondents_id', '{{group_respondents}}', 'manager_id', Yii::app()->getModule('user')->tableUsers, 'id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('client_group_respondents_id', '{{group_respondents}}', 'client_id', Yii::app()->getModule('user')->tableClients, 'id', 'CASCADE', 'RESTRICT');

            $this->createTable(Yii::app()->getModule('respondent')->tableRespondentsGroupUsers, array(
                    'group_respondents_id'    => 'INT(3)',
                    'respondents_id'          => 'INT(3)',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_group_respondents_id', '{{link_users_group_respondents}}', 'group_respondents_id', Yii::app()->getModule('respondent')->tableRespondentsGroup, 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('link_respondents_id', Yii::app()->getModule('respondent')->tableRespondentsGroupUsers, 'respondents_id', Yii::app()->getModule('respondent')->tableRespondents, 'id', 'NO ACTION', 'NO ACTION');
	}


	public function safeDown()
	{
            $this->dropTable('{{link_users_group_respondents}}');
            $this->dropTable('{{group_respondents}}');
	}
}