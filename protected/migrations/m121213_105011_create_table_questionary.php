<?php

class m121213_105011_create_table_questionary extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	public function safeUp()
	{
            $this->createTable('{{applications}}', array(
                    'id'             		=> 'pk',
                    'respondent_id'             => 'INT(3)',
                    'quiz_id'                   => 'INT(3)',
                    'state'                     => 'ENUM("todo","done","close","reject","appeal")',
                    'date_created'    		=> 'DATETIME',
                    'date_filled'    		=> 'DATETIME',
                    'date_closed'    		=> 'DATETIME',
                    'check_question_id'         => 'VARCHAR(50)',
                    'check_question_order'      => 'INT',
                    'check_question_group_id'   => 'INT(3)',
                    'check_answers_id'          => 'VARCHAR(50)',
                    'is_true_answer'            => 'BOOLEAN',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_applications_respondent_id', '{{applications}}', 'respondent_id', '{{respondents}}', 'id', 'SET NULL', 'RESTRICT');
            $this->addForeignKey('link_applications_quiz_id', '{{applications}}', 'quiz_id', '{{quiz}}', 'quiz_id', 'SET NULL', 'RESTRICT');
            $this->addForeignKey('link_applications_check_question_id', '{{applications}}', 'check_question_id', '{{dict_check_questions}}', 'id', 'SET NULL', 'RESTRICT');
            $this->addForeignKey('link_applications_check_question_group_id', '{{applications}}', 'check_question_group_id', '{{group_questions}}', 'id', 'SET NULL', 'RESTRICT');
            $this->addForeignKey('link_applications_check_answers_id', '{{applications}}', 'check_answers_id', '{{dict_check_answers}}', 'id', 'SET NULL', 'RESTRICT');

            $this->createTable('{{application_answers}}', array(
                    'id'             		=> 'pk',
                    'application_id'            => 'INT(3)',
                    'question_id'               => 'VARCHAR(50)',
                    'answer_id'                 => 'VARCHAR(50)',
                    'answer_text'    		=> 'VARCHAR(500)',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_application_answers_application_id', '{{application_answers}}', 'application_id', '{{applications}}', 'id', 'CASCADE', 'RESTRICT');
            //$this->addForeignKey('link_application_answers_question_id', '{{application_answers}}', 'question_id', '{{questions}}', 'id', 'CASCADE', 'RESTRICT');
            //$this->addForeignKey('link_application_answers_answer_id', '{{application_answers}}', 'answer_id', '{{answers}}', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
            $this->dropTable('{{applications}}');
            $this->dropTable('{{application_answers}}');
	}
}