<?php

class DefaultController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
			'accessControl', // perform access control for CRUD operations
		));
	}

        /**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','applications'),
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
                $dataProvider=new CActiveDataProvider('Respondent', array(
                    'criteria'=>array(
                    ),
                    'pagination'=>array(
                        'pageSize'=>Yii::app()->controller->module->respondent_page_size,
                    ),
		));

		$this->render('/respondent/index',array(
			'dataProvider'=>$dataProvider,
		));
	}

        /**
	 * View model.
	 */
	public function actionView($id)
	{
            $model = Respondent::model()->findByPk($id);
            $this->render('/respondent/view',array(
                    'model'=>$model,
            ));
	}

        /**
	 * Displays Lists a particular model.
	 */
	public function actionApplications($id)
	{
                $model = Respondent::model()->findByPk($id);
                $this->render('/respondent/applications',array(
                        'model'=>$model,
                ));
	}
}