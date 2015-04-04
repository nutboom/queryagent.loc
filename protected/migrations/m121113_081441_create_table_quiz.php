<?php

class m121113_081441_create_table_quiz extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	/*public function up()
	{
	}

	public function down()
	{
		echo "m121113_081441_create_table_quiz does not support migration down.\n";
		return false;
	}*/


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable('{{quiz}}', array(
                'quiz_id'             => 'pk',
                'title'               => 'VARCHAR(150)',
                'client_id'           => 'INT(3)',
                'anonymous_client'    => 'BOOLEAN',
                'manager_id'          => 'INT(3)',
                'fill_time'           => 'string',
                'description'         => 'TEXT',
                'type'                => 'ENUM("quiz","mission")',
                'money'               => 'FLOAT',
                'karma'               => 'FLOAT',
                'date_created'        => 'DATETIME',
                'date_start'          => 'DATETIME',
                'date_stop'           => 'DATETIME',
                'deadline'            => 'DATETIME',
                'state'               => 'ENUM("edit","work","fill")',
                'needs_confirmation'  => 'BOOLEAN',
            ), $this->MySqlOptions);
            $this->addForeignKey('client_quiz_id', '{{quiz}}', 'client_id', '{{clients}}', 'id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('webuser_quiz_id', '{{quiz}}', 'manager_id', '{{users}}', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
            $this->dropTable('{{quiz}}');
	}
}