<?php

class SmstplController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

        /**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('deny',  // deny all users
                'actions'=>array('view'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index'),
				'users'=>Yii::app()->getModule('user')->getManagers(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex() {
		if (isset($_POST['Template'])) {
			foreach($_POST['Template'] as $type => $content) {
				$template = SmsTpl::model()->find("user = :user AND type = :type", array(":user" => Yii::app()->user->id, ":type" => $type));
				if ($template) {
					$template->template = $content;
					$template->save();
				}
				else {
					$template = new SmsTpl;
					$template->user = Yii::app()->user->id;
					$template->type = $type;
					$template->template = $content;
					$template->save();
				}
			}

			Yii::app()->user->setFlash('success', Yii::t('app', 'Templates successfully updated'));
		}

		$this->render('index',array(
		));
	}
}