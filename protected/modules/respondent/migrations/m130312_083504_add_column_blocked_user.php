<?php

class m130312_083504_add_column_blocked_user extends CDbMigration
{
	public function safeUp()
	{
            $this->addColumn(Yii::app()->getModule('respondent')->tableRespondents, 'blocked', 'TINYINT(1)');
	}

	public function safeDown()
	{
            $this->dropColumn(Yii::app()->getModule('respondent')->tableRespondents, 'blocked');
	}
}