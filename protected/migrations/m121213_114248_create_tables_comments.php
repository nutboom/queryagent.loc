<?php

class m121213_114248_create_tables_comments extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	public function safeUp()
	{
            $this->createTable('{{application_comments}}', array(
                    'id'             	=> 'pk',
                    'application_id'    => 'INT(3)',
                    'date_created'    	=> 'DATETIME',
                    'state'    		=> 'ENUM("todo","done","close","reject","appeal")',
                    'role'    		=> 'ENUM("respondent","client","admin")',
                    'text'    		=> 'TEXT',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_application_comments_application_id', '{{application_comments}}', 'application_id', '{{applications}}', 'id', 'CASCADE', 'RESTRICT');

            $this->createTable('{{quiz_comments}}', array(
                    'id'             	=> 'pk',
                    'respondent_id'    => 'INT(3)',
                    'quiz_id'    => 'INT(3)',
                    'date_created'    	=> 'DATETIME',
                    'text'    		=> 'TEXT',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_quiz_comments_respondent_id', '{{quiz_comments}}', 'respondent_id', '{{respondents}}', 'id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('link_quiz_comments_quiz_id', '{{quiz_comments}}', 'quiz_id', '{{quiz}}', 'quiz_id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
            $this->dropTable('{{application_comments}}');
            $this->dropTable('{{quiz_comments}}');
	}
}