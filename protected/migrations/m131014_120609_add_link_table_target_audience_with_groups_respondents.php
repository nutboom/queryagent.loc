<?php

class m131014_120609_add_link_table_target_audience_with_groups_respondents extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable('{{link_target_audience_group_respondents}}', array(
                    'target_audience_id' => 'INT',
                    'group_id'	 		 => 'INT',
                ), $this->MySqlOptions);
	}

	public function safeDown()
	{
            $this->dropTable('{{link_target_audience_group_respondents}}');
	}
}