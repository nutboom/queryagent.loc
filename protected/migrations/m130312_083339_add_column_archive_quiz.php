<?php

class m130312_083339_add_column_archive_quiz extends CDbMigration
{
	public function safeUp()
	{
            $this->addColumn('{{quiz}}', 'archive', 'TINYINT(1)');
	}

	public function safeDown()
	{
            $this->dropColumn('{{quiz}}', 'archive');
	}
}