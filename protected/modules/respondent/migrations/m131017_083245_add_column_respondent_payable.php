<?php

class m131017_083245_add_column_respondent_payable extends CDbMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->addColumn(Yii::app()->getModule('respondent')->tableRespondents, 'payable', 'TINYINT(1)');
	}

	public function safeDown()
	{
            $this->dropColumn(Yii::app()->getModule('respondent')->tableRespondents, 'payable');
	}
}