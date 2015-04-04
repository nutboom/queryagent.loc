<?php

class m130204_111211_add_push_notofication_token extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->addColumn(Yii::app()->getModule('respondent')->tableSessions, 'device_token', 'VARCHAR(120)');
	}

	public function safeDown()
	{
            $this->dropColumn(Yii::app()->getModule('respondent')->tableSessions, 'device_token');
	}
}