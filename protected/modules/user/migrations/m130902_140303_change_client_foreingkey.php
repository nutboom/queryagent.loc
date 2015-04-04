<?php

class m130902_140303_change_client_foreingkey extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->dropForeignKey('client', Yii::app()->getModule('user')->tableUsersClients);
            $this->addForeignKey('client', Yii::app()->getModule('user')->tableUsersClients, 'clients_id', Yii::app()->getModule('user')->tableClients, 'id', 'SET NULL', 'SET NULL');
	}
}