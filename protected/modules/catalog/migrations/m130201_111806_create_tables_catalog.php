<?php

class m130201_111806_create_tables_catalog extends CDbMigration
{
        protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable(Yii::app()->getModule('catalog')->tableDictEducation, array(
                'dict_education_id'    => 'pk',
                'title'                => 'VARCHAR(150)',
            ), $this->MySqlOptions);

            $this->createTable(Yii::app()->getModule('respondent')->tableRespondentsEducations, array(
                "respondent_id" => "int(3)",
                "education_id" => "int(3)",
            ), $this->MySqlOptions);
            $this->addForeignKey('link_respondent_id', Yii::app()->getModule('respondent')->tableRespondentsEducations, 'respondent_id', Yii::app()->getModule('respondent')->tableRespondents, 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('link_education_id', Yii::app()->getModule('respondent')->tableRespondentsEducations, 'education_id', Yii::app()->getModule('catalog')->tableDictEducation, 'dict_education_id', 'NO ACTION', 'NO ACTION');

            $this->createTable(Yii::app()->getModule('catalog')->tableDictScope, array(
                'dict_scope_id'    => 'pk',
                'title'            => 'VARCHAR(150)',
                'is_job'           => 'BOOLEAN',
            ), $this->MySqlOptions);
            $this->addForeignKey('respondent_scope_id', Yii::app()->getModule('respondent')->tableRespondents, 'scope_id', Yii::app()->getModule('catalog')->tableDictScope, 'dict_scope_id', 'NO ACTION', 'NO ACTION');

            $this->createTable(Yii::app()->getModule('catalog')->tableDictJobPosition, array(
                'dict_job_position_id'    => 'pk',
                'title'                   => 'VARCHAR(150)',
            ), $this->MySqlOptions);
            $this->addForeignKey('respondent_position_id', Yii::app()->getModule('respondent')->tableRespondents, 'position_id', Yii::app()->getModule('catalog')->tableDictJobPosition, 'dict_job_position_id', 'NO ACTION', 'NO ACTION');

            $this->createTable(Yii::app()->getModule('catalog')->tableDictCountry, array(
                'dict_country_id'    => 'pk',
                'title'              => 'VARCHAR(150)',
            ), $this->MySqlOptions);
            $this->addForeignKey('respondent_country_id', Yii::app()->getModule('respondent')->tableRespondents, 'country_id', Yii::app()->getModule('catalog')->tableDictCountry, 'dict_country_id', 'NO ACTION', 'NO ACTION');

            $this->createTable(Yii::app()->getModule('catalog')->tableDictCity, array(
                'dict_city_id'    => 'pk',
                'title'           => 'VARCHAR(150)',
                'country_id'      => 'INT(3)',
            ), $this->MySqlOptions);
            $this->addForeignKey('country_city_id', Yii::app()->getModule('catalog')->tableDictCity, 'country_id', Yii::app()->getModule('catalog')->tableDictCountry, 'dict_country_id', 'CASCADE', 'RESTRICT');
            $this->addForeignKey('respondent_city_id', Yii::app()->getModule('respondent')->tableRespondents, 'city_id', Yii::app()->getModule('catalog')->tableDictCity, 'dict_city_id', 'NO ACTION', 'NO ACTION');

            $this->createTable(Yii::app()->getModule('catalog')->tableDictCheckQuestions, array(
                'id'        	=> 'VARCHAR(50)',
                'text'		=> 'TEXT',
                'PRIMARY KEY (`id`)'
            ), $this->MySqlOptions);
            $this->createTable(Yii::app()->getModule('catalog')->tableDictCheckAnswers, array(
                'id'            => 'VARCHAR(50)',
                'question_id'   => 'VARCHAR(50)',
                'text'		=> 'TEXT',
                'is_true'	=> 'BOOL',
                'PRIMARY KEY (`id`)'
            ), $this->MySqlOptions);
            $this->addForeignKey('dict_check_answers_question_id', Yii::app()->getModule('catalog')->tableDictCheckAnswers, 'question_id', Yii::app()->getModule('catalog')->tableDictCheckQuestions, 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
            $this->dropTable(Yii::app()->getModule('respondent')->tableRespondentsEducations);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictEducation);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictScope);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictJobPosition);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictCountry);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictCity);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictCheckQuestions);
            $this->dropTable(Yii::app()->getModule('catalog')->tableDictCheckAnswers);
	}
}