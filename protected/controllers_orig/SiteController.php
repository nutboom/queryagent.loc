<?php

class SiteController extends Controller
{

    public function init()
    {
        if(preg_match('/\/page\/.*/i', $_SERVER['REQUEST_URI']))
            $this->layout = false;
    }

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
        	array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','search'),
				'users'=>array('@'),
			),
            array('deny',
                'actions'=>array('contact'),
                'users'=>array('*'),
            ),
        );
    }
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		if (Yii::app()->user->isGuest) {
			$this->redirect(array("/login"));
		}

		$criteria=new CDbCriteria;
		$criteria->compare('type', Quiz::TYPE_GENERAL);
		$criteria->compare('state', Quiz::STATE_WORK);
		$criteria->compare('is_deleted', Quiz::NO_DELETED);
		$criteria->order = 'date_created DESC';
		$criteria->limit = 3;
		if(!Yii::app()->getModule('user')->isAdmin()){
			$criteria->compare('manager_id', Yii::app()->user->id);
		}
		
		$quizs= new CActiveDataProvider('Quiz', array('criteria'=>$criteria,));

		$criteria=new CDbCriteria;
		$criteria->compare('type', Quiz::TYPE_MISSION);
		$criteria->compare('state', Quiz::STATE_WORK);
		$criteria->compare('is_deleted', Quiz::NO_DELETED);
		$criteria->order = 'date_created DESC';
		$criteria->limit = 3;
		if(!Yii::app()->getModule('user')->isAdmin()){
			$criteria->compare('manager_id', Yii::app()->user->id);
		}
		$missions= new CActiveDataProvider('Quiz', array('criteria'=>$criteria,));


		$this->render('index',array(
			'quizs'=>$quizs,
			'missions'=>$missions
		));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionCourse() {

		$this->render('course');
	}

	public function actionSearch() {
		$query = Trim($_POST['text']);
		/*$query	=	preg_replace("/[^\d\w ]+/i", "", $query);
		$array	=	Explode(" ", $query);
		$query	=	"(";
		foreach($array as $key) $query .= "`{query}` LIKE '%".$key."%' OR ";
		$query	=	substr($query, 0, -4).")";*/

		$results = array();
		$connection=Yii::app()->db;

		$types = array('quiz', 'mission');
		foreach($types as $type) {
			$sql = "
				SELECT * FROM `tbl_quiz`
				WHERE
					`manager_id` = :manager
						AND
					`title` LIKE :query
						AND
					`type` = :type
			";
			$command	=	$connection->createCommand($sql);
			$command->bindValue(':type', $type);
			$command->bindValue(':query', "%".$query."%");
			$command->bindValue(':manager', Yii::app()->user->id);
			$data = $command->query();

			while(($row=$data->read()) !== false) {
				$results[]	=	array(
					"text"	=>	$row['title'],
					"url"	=>	$this->createUrl('/'.$type.'/'.$row['quiz_id'].'/update')
				);
			}
		}

		$sql = "
			SELECT * FROM `tbl_group_respondents`
			WHERE
				`manager_id` = :manager
					AND
				`title` LIKE :query
		";
		$command	=	$connection->createCommand($sql);
		$command->bindValue(':query', "%".$query."%");
		$command->bindValue(':manager', Yii::app()->user->id);
		$data = $command->query();

		while(($row=$data->read()) !== false) {
			$results[]	=	array(
				"text"	=>	$row['title'],
				"url"	=>	$this->createUrl('/respondent/groups/update/id/'.$row['id'])
			);
		}

		echo json_encode($results);
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
}