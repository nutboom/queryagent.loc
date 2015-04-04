<?php

class m121115_121748_create_table_target_audience extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	/*public function up()
	{
	}

	public function down()
	{
		echo "m121115_121748_create_table_target_audience does not support migration down.\n";
		return false;
	}*/


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->createTable('{{target_audience}}', array(
                        'id' 						=> 'pk',
                        'quiz_id' 					=> 'INT(3) DEFAULT 0',
                        'age_from' 					=> 'INT DEFAULT 0',
                        'age_to' 					=> 'INT DEFAULT 0',
                        'income_from' 				=> 'INT DEFAULT 0',
                        'income_to' 				=> 'INT DEFAULT 0',
                        'gender' 					=> 'ENUM("male", "female", "any")',
                        'marital_state' 			=> 'ENUM("single", "married", "divorced", "any")',
                        'minimal_user_state_id' 	=> 'INT(3) DEFAULT 0',
                        'count_limit' 				=> 'INT',
                ), $this->MySqlOptions);
                $this->addForeignKey('quiz', '{{target_audience}}', 'quiz_id', '{{quiz}}', 'quiz_id', 'CASCADE', 'RESTRICT');
                $this->addForeignKey('user_status', '{{target_audience}}', 'minimal_user_state_id', Yii::app()->getModule('respondent')->tableRespondentsStatuses, 'id', 'SET NULL', 'RESTRICT');

                $this->createTable('{{link_education_target_audience}}', array(
                    'target_audience_id' => 'INT',
                    'education_id' 		 => 'INT',
                ), $this->MySqlOptions);

                $this->createTable('{{link_target_audience_job_position}}', array(
                    'target_audience_id' => 'INT',
                    'job_position_id'	 => 'INT',
                ), $this->MySqlOptions);

                $this->createTable('{{link_target_audience_scope}}', array(
                    'target_audience_id' => 'INT',
                    'scope_id'	 		 => 'INT',
                ), $this->MySqlOptions);

                $this->createTable('{{link_target_audience_country}}', array(
                    'target_audience_id' => 'INT',
                    'country_id'	 	 => 'INT',
                ), $this->MySqlOptions);

                $this->createTable('{{link_target_audience_city}}', array(
                    'target_audience_id' => 'INT',
                    'city_id'	 		 => 'INT',
                ), $this->MySqlOptions);
	}

	public function safeDown()
	{
		$this->dropTable('{{target_audience}}');
		$this->dropTable('{{link_education_target_audience}}');
		$this->dropTable('{{link_target_audience_job_position}}');
		$this->dropTable('{{link_target_audience_scope}}');
		$this->dropTable('{{link_target_audience_country}}');
		$this->dropTable('{{link_target_audience_city}}');
	}
}