<?php

class m121121_060754_create_structure_quiz_questions_answers extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	/*public function up()
	{
	}

	public function down()
	{
		echo "m121121_060754_create_structure_quiz_questions_answers does not support migration down.\n";
		return false;
	}*/

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable('{{group_questions}}', array(
                    'id'             		=> 'pk',
                    'quiz_id'               => 'INT(3)',
                    'condition_question_id' => 'VARCHAR(50)',
                    'orderby'    				=> 'INT',
            ), $this->MySqlOptions);

            $this->createTable('{{questions}}', array(
                    'id'        	=> 'VARCHAR(50)',
                    'group_id'  	=> 'INT(3)',
                    'text'			=> 'TEXT',
                    'type'    		=> 'ENUM("open", "close", "semiclose", "scale_close", "scale_score", "close_multiple_choice", "answer_photo")',
                    'orderby'    		=> 'INT',
                    'scaled_size'	=> 'INT',
                    'is_class'    	=> 'BOOLEAN',
                    'PRIMARY KEY (`id`)'
            ), $this->MySqlOptions);

            $this->addForeignKey('group_questions_quiz_id', '{{group_questions}}', 'quiz_id', '{{quiz}}', 'quiz_id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('group_questions_condition_question_id', '{{group_questions}}', 'condition_question_id', '{{questions}}', 'id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('questions_group_id', '{{questions}}', 'group_id', '{{group_questions}}', 'id', 'CASCADE', 'RESTRICT');

            $this->createTable('{{question_media}}', array(
                    'id'            => 'pk',
                    'question_id'   => 'VARCHAR(50)',
                    'link' 			=> 'VARCHAR(150)',
            ), $this->MySqlOptions);
            $this->addForeignKey('pictures_question_id', '{{question_media}}', 'question_id', '{{questions}}', 'id', 'CASCADE', 'RESTRICT');

            $this->createTable('{{answers}}', array(
                    'id'            => 'VARCHAR(50)',
                    'question_id'   => 'VARCHAR(50)',
                    'text' 			=> 'TEXT',
                    'orderby' 		=> 'INT',
                    'PRIMARY KEY (`id`)'
            ), $this->MySqlOptions);
            $this->addForeignKey('answers_question_id', '{{answers}}', 'question_id', '{{questions}}', 'id', 'CASCADE', 'RESTRICT');

            $this->createTable('{{link_group_questions_answers}}', array(
                    'group_questions_id'    => 'INT(3)',
                    'answers_id'   			=> 'VARCHAR(50)',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_group_question_id', '{{link_group_questions_answers}}', 'group_questions_id', '{{group_questions}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('link_answers_id', '{{link_group_questions_answers}}', 'answers_id', '{{answers}}', 'id', 'NO ACTION', 'NO ACTION');

            $this->createTable('{{link_target_audience_classif_answers}}', array(
                    'target_audience_id'    => 'INT(3)',
                    'answers_id'   			=> 'VARCHAR(50)',
            ), $this->MySqlOptions);
            $this->addForeignKey('link_target_audience_id', '{{link_target_audience_classif_answers}}', 'target_audience_id', '{{target_audience}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('link_to_answers_id', '{{link_target_audience_classif_answers}}', 'answers_id', '{{answers}}', 'id', 'NO ACTION', 'NO ACTION');
	}

	public function safeDown()
	{
		$this->dropTable('{{group_questions}}');
		$this->dropTable('{{questions}}');
		$this->dropTable('{{question_media}}');
		$this->dropTable('{{answers}}');
		$this->dropTable('{{link_group_questions_answers}}');
		$this->dropTable('{{link_target_audience_classif_answers}}');
	}
}