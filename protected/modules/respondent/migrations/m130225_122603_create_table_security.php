<?php

class m130225_122603_create_table_security extends CDbMigration
{
        protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable('{{captcha}}', array(
                    'id'             	=> 'pk',
                    'respondent_id'    	=> 'INT(3)',
                    'code'              => 'VARCHAR(30)',
                    'img_name'    	=> 'VARCHAR(30)',
                    'full_path'    	=> 'VARCHAR(230)',
                    'date_created'    	=> 'DATETIME',
                    'IP'    		=> 'VARCHAR(30)',
            ), $this->MySqlOptions);

            $this->createTable('{{history_respondent}}', array(
                    'id'             	=> 'pk',
                    'key_action'        => 'ENUM("code_phone")',
                    'action_do'            => 'ENUM("edit")',
                    'respondent_id'    	=> 'INT(3)',
                    'date_created'    	=> 'DATETIME',
            ), $this->MySqlOptions);
	}

	public function safeDown()
	{
            $this->dropTable('{{captcha}}');
            $this->dropTable('{{history_respondent}}');
	}
}