<?php

class CatalogModule extends CWebModule
{
        public $dictEducationUrl = array("/catalog/DictEducation");
	public $dictJobPositionUrl = array("/catalog/DictJobPosition");
	public $dictScopeUrl = array("/catalog/DictScope");
	public $dictCityUrl = array("/catalog/DictCity");
	public $dictCountryUrl = array("/catalog/DictCountry");
	public $dictCheckQuestionsUrl = array("/catalog/DictCheckQuestions");

        public $tableDictEducation = '{{dict_education}}';
	public $tableDictScope = '{{dict_scope}}';
	public $tableDictJobPosition = '{{dict_job_position}}';
	public $tableDictCountry = '{{dict_country}}';
	public $tableDictCity = '{{dict_city}}';
        public $tableDictCheckQuestions = '{{dict_check_questions}}';
	public $tableDictCheckAnswers = '{{dict_check_answers}}';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'catalog.models.*',
			'catalog.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
