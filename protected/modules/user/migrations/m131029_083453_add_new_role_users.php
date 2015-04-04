<?php

class m131029_083453_add_new_role_users extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->addColumn(Yii::app()->getModule('user')->tableUsers, 'marketer', 'TINYINT(1)');
        $this->update(Yii::app()->getModule('user')->tableUsers, array('marketer'=>0));
	}

	public function safeDown()
	{
        $this->dropColumn(Yii::app()->getModule('user')->tableUsers, 'marketer');
	}
}