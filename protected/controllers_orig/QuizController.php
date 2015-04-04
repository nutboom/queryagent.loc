<?php

class QuizController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
	public function accessRules() {
		return array(
			array('deny',  // deny all users
				'actions'=>array('view','admin'),
				'users'=>array('*'),
			),

			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','comments','statistics','export','respondents','graph','delete', 'excel','pdf' ),
				'users'=>array('@'),
			),

			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','precreate','collection','launch','clone','graph','delete'),
				'users'=>Yii::app()->getModule('user')->getManagers(),
			),

			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionPrecreate($type) {
		$this->render('precreate',array(
			'type'=>$type,
			//'user'=>$user
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($type) {
		$model=new Quiz;
		$user = Yii::app()->user->user(Yii::app()->user->id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->state = Quiz::STATE_EDIT;
		$model->type = $type;
		$model->money = 0;
		$model->karma = 0;

		if(isset($_POST['Quiz'])) {
			$model->attributes=$_POST['Quiz'];

			if($model->save()) {
				$this->redirect(array($type."/".$model->quiz_id."/targetAudience"));
    		}
		}

		$this->render('create',array(
			'model'=>$model,
			'user'=>$user
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model=$this->loadModel($id);

		if (Yii::app()->getModule('user')->isAdmin()) {
			$user = Yii::app()->user->user($model->manager_id);
		}
		else {
			$user = Yii::app()->user->user(Yii::app()->user->id);
		}



		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Quiz'])) {
			$model->attributes=$_POST['Quiz'];
			$model->isSendMessenge = $_POST['Quiz']['isSendMessenge'];

			if($model->save()) {
				$this->refresh();
    		}
		}

		if (Yii::app()->getModule('user')->isAdmin()) {
			$typeState = 'QuizState';
		}
		elseif ($model->state == Quiz::STATE_WORK) {
			$typeState = 'QuizStateWork';
		}
		elseif ($model->state == Quiz::STATE_MODERATION) {
			$typeState = 'QuizStateModeration';
		}
  		else {
  			if(count($model->audience) > 0 && $model->countQuestions() > 0) {
  				$typeState = 'QuizStateNotFill';
  			}
		}

		$this->render('update',array(
			'model'=>$model,
			'user'=>$user,
			'nameTypeState'=>$typeState,
		));
	}

	/*
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->is_deleted = 1;
		$model->save();
		
		$this->redirect(array("/".$model->type));
	}

	public function actionGraph() {
		if(Yii::app()->request->isPostRequest) {
			$quiz=$this->loadModel($_POST['quiz']);

	        $start		=	Explode("-", Current(Explode(" ", $quiz->date_start)));
	        $startquiz	=	mktime (0, 0, 1, $start[1], $start[2], $start[0]);
	        if ((Time() - $startquiz) >= 60*60*24*30) {
	        	$timestamp	=	Time() - 60*60*24*30;
	        }
	        else {
	        	$timestamp	=	$startquiz;
	        }

			$connection=Yii::app()->db;
			$json = Array();

			For ($i = $timestamp; $i <= ($timestamp+60*60*24*30); $i += 60*60*24) {
				$date = date("Y-m-d", $i);

				$sql = "
					SELECT COUNT(*) FROM `tbl_applications`
					WHERE
						`quiz_id` = '".$quiz->quiz_id."'
							AND
						`date_created` BETWEEN '".$date."'
							AND
						DATE_ADD('".$date."', INTERVAL 1 DAY)
				";
				$command=$connection->createCommand($sql);
				$count = current($command->query()->read());

				array_push($json, array("date" => Date("Y-m-d", $i), "value" => $count*1));
	        }

	        echo json_encode($json);
	    }
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionCollection($type, $id) {
		$model=$this->loadModel($id);

		if(isset($_POST['Quiz'])) {
			$model->attributes=$_POST['Quiz'];

			if ($model->save()) {
    		}

			$this->redirect(array($type.'/'.$model->quiz_id.'/launch'));
		}

		$this->render('collection',array(
			'model'=>$model
		));
	}

	public function actionLaunch($type, $id) {
		$model=$this->loadModel($id);


		if(isset($_POST['Quiz'])) {
	  		if ($model->state == Quiz::STATE_EDIT) {
	  			if(count($model->audience) > 0 && $model->countQuestions() > 0) {
	  				$model->attributes=$_POST['Quiz'];

				  	// if quiz use respondents in all base set moderation status
				  	$base = TargetAudience::haveBaseAudience($model);
				  	if ($base == true) {
				  		$model->state = Quiz::STATE_MODERATION;

						// mail to chief
						$link = $this->createAbsoluteUrl('/quiz/'.$model->quiz_id.'/update');
						UserModule::sendMail("vadimshavlukevich@yandex.ru", "We have new quiz on moderation", "<a href='".$link."'>link</a>");

				  	}
				  	else {
				  		$model->state = Quiz::STATE_WORK;
				  	}


	  				if($model->save()) {
	  					if ($type == "quiz") {
	  						Yii::app()->user->setFlash('success', Yii::t('app', 'Quiz successfully launched and is on the performance of the respondents'));
	  					}
	  					else {
	  						Yii::app()->user->setFlash('success', Yii::t('app', 'Mission successfully launched and is on the performance of the respondents'));
	  					}
	  				}
	  			}
			}
			else {
            	Yii::app()->user->setFlash('fail', Yii::t('app', 'We can not launch your quiz'));
			}
		}

		$this->render('launch',array(
			'model'=>$model
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($type) {
		$archive = null;
		$criteria=new CDbCriteria;
		$criteria->compare('type', $type);

		$criteria->compare('is_deleted', Quiz::NO_DELETED);
		if (isset($_GET['client'])) $criteria->compare('client_id', $_GET['client']);
		if (isset($_GET['state'])) $criteria->compare('state', $_GET['state']);

		if(!Yii::app()->getModule('user')->isAdmin()){
			#$clients = Yii::app()->user->getClients(Yii::app()->user->id);
			#$criteria->addInCondition('client_id', array_keys($clients));
			$criteria->compare('manager_id', Yii::app()->user->id);
		}

		$manager = null;
		if(Yii::app()->getModule('user')->isAdmin() && $_GET['manager_id']){
			$criteria->compare('manager_id', $_GET['manager_id']);
			$manager	=	User::model()->findByPk($_GET['manager_id']);
		}

		if (isset($_GET['archive'])) {
			$criteria->compare('archive','1',true);
		}
		else {
			$criteria->addCondition('archive IS NULL OR archive = 0');
		}

		$criteria->order = 'date_created DESC';

        /*
		$criteria->compare('title',$this->title,true);
		$criteria->compare('DATE(date_created)', Utils::pack_date($this->date_created),true);
        $criteria->compare('DATE(deadline)', Utils::pack_date($this->deadline),true);
        */



		// получаем количество записей
		$count = Quiz::model()->count($criteria);
		// создаем модель для пагинации pagination
		$pages = new CPagination($count);
		$pages->setPageSize(10); // устанавливаем количество записей на странице
		$pages->applyLimit($criteria); // привязываем Criteria

		$provider= new CActiveDataProvider('Quiz', array(
			'criteria'=>$criteria,
		));
		$provider->setPagination(false);

		$this->render('index',array(
			'type'=>$type,
			'archive'=>$archive,
			'provider'=>$provider,
			'pages' => $pages,
			'count'=>$count,
			'manager'=>$manager,
		));
	}

	private function genHash() {
		$string	=	"{".SubStr(Md5(Rand(1, Time())), 0, 8)."-";
		$string	.=	SubStr(Md5(Rand(1, Time())), 0, 4)."-";
		$string	.=	SubStr(Md5(Rand(1, Time())), 0, 4)."-";
		$string	.=	SubStr(Md5(Rand(1, Time())), 0, 4)."-";
		$string	.=	SubStr(Md5(Rand(1, Time())), 0, 12)."}";

		return strtoupper($string);
	}

	public function actionClone() {
		$model=$this->loadModel($_POST['quiz']);
		$connection=Yii::app()->db;

		// create new quiz data
		$quiz = new Quiz;
		$quiz->attributes=$model->attributes;
		$quiz->title = "Копия: ".$model->title;
		$quiz->state = "edit";
		$quiz->archive = "0";
		if ($quiz->save()) {
			// copys audience
			$criteria = new CDbCriteria;
			$criteria->compare('quiz_id', $model->quiz_id);
			$provider= new CActiveDataProvider('TargetAudience', array('criteria'=>$criteria));
			foreach($provider->getData() as $audience) {
				$target = new TargetAudience;
				$target->attributes=$audience->attributes;
				$target->quiz_id = $quiz->quiz_id;
				$target->save();

                // copy 'tbl_link_education_target_audience'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_education_target_audience` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_education_target_audience` SET
						`target_audience_id` = '".$target->id."',
						`education_id` = '".$row['education_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_city'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_city` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_city` SET
						`target_audience_id` = '".$target->id."',
						`city_id` = '".$row['city_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_classif_answers'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_classif_answers` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_classif_answers` SET
						`target_audience_id` = '".$target->id."',
						`answers_id` = '".$row['answers_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_country'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_country` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_country` SET
						`target_audience_id` = '".$target->id."',
						`country_id` = '".$row['country_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_group_respondents'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_group_respondents` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_group_respondents` SET
						`target_audience_id` = '".$target->id."',
						`group_id` = '".$row['group_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_job_position'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_job_position` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_job_position` SET
						`target_audience_id` = '".$target->id."',
						`job_position_id` = '".$row['job_position_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_scope'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_scope` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_scope` SET
						`target_audience_id` = '".$target->id."',
						`scope_id` = '".$row['scope_id']."'
					")->execute();
				}

				// copy 'tbl_link_target_audience_scope'
				$command=$connection->createCommand("SELECT * FROM `tbl_link_target_audience_scope` WHERE `target_audience_id` = '".$audience->id."'");
				$data = $command->query();
				while(($row=$data->read())!==false) {
					$connection->createCommand("
						INSERT INTO `tbl_link_target_audience_scope` SET
						`target_audience_id` = '".$target->id."',
						`scope_id` = '".$row['scope_id']."'
					")->execute();
				}

			}



			// copys group of questions
			$arrayAnswers	=	Array();
			$arrayQuestions	=	Array();
			$command=$connection->createCommand("SELECT * FROM `tbl_group_questions` WHERE `quiz_id` = '".$model->quiz_id."'");
			$groups = $command->query();
			while(($group=$groups->read())!==false) {
				// save group of questions
				$condition	=	($group['condition_question_id']) ? "'".$arrayQuestions[$group['condition_question_id']]."'" : "null";
				$connection->createCommand("
					INSERT INTO `tbl_group_questions` SET
						`quiz_id`				=	'".$quiz->quiz_id."',
						`condition_question_id`	=	".$condition.",
						`orderby`				=	'".$group['orderby']."'
				")->execute();
				$idGroup	=	$connection->lastInsertID;

				// copys link in group questions at answer
				$command=$connection->createCommand("SELECT * FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$group['id']."'");
				$links = $command->query();
				while(($link=$links->read())!==false) {
					// save link
					$connection->createCommand("
						INSERT INTO `tbl_link_group_questions_answers` SET
							`group_questions_id`	=	'".$idGroup."',
							`answers_id`			=	'".$arrayAnswers[$link['answers_id']]."'
					")->execute();
				}

				// copys questions
				$command=$connection->createCommand("SELECT * FROM `tbl_questions` WHERE `group_id` = '".$group['id']."'");
				$questions = $command->query();
				while(($question=$questions->read())!==false) {
					// save question
					$questionHash	=	$this->genHash();
					$connection->createCommand("
						INSERT INTO `tbl_questions` SET
							`id`			=	'".$questionHash."',
							`group_id`		=	'".$idGroup."',
							`text`			=	'".$question['text']."',
							`type`			=	'".$question['type']."',
							`orderby`		=	'".$question['orderby']."',
							`scaled_size`	=	'".$question['scaled_size']."',
							`is_class`		=	'".$question['is_class']."'
					")->execute();

					// save dumper question
					$arrayQuestions[$question['id']] = $questionHash;

					// copys answers
					$command=$connection->createCommand("SELECT * FROM `tbl_answers` WHERE `question_id` = '".$question['id']."'");
					$answers = $command->query();
					while(($answer=$answers->read())!==false) {
						// save answer
						$answerHash	=	$this->genHash();
						$connection->createCommand("
							INSERT INTO `tbl_answers` SET
								`id`			=	'".$answerHash."',
								`question_id`	=	'".$questionHash."',
								`text`			=	'".$answer['text']."',
								`orderby`		=	'".$answer['orderby']."'
						")->execute();

						// save dumper answers
						$arrayAnswers[$answer['id']] = $answerHash;
					}

					// copys quesion media
					$command=$connection->createCommand("SELECT * FROM `tbl_question_media` WHERE `question_id` = '".$question['id']."'");
					$medias = $command->query();
					while(($media=$medias->read())!==false) {
						// save media
						$connection->createCommand("
							INSERT INTO `tbl_question_media` SET
								`question_id`	=	'".$questionHash."',
								`link`			=	'".$media['link']."'
						")->execute();

					}
				}
			}





			$this->redirect(array("/".$quiz->type."/".$quiz->quiz_id."/update"));
		}

	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Quiz('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Quiz']))
			$model->attributes=$_GET['Quiz'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

        /**
	 * List comments a  particular model.
	 */
	public function actionComments($id)
	{
            $model=$this->loadModel($id);

            $this->render('comments',array(
			'model'=>$model,
		));
        }

        /**
	 * Statistics a particular model.
	 */
	public function actionStatistics($id)
	{
            include("pChart/pData.class");
            include("pChart/pChart.class");

            $model=$this->loadModel($id);

            $resStat = array();
            if(count($model->applications) > 0){
                $arrStat = array();
                $audiences = $model->audience;
                $applications = array();
                $arrAudience = array();

                foreach ($audiences as $a => $audience) {
                    $respondents = $audience->getRespondents();
                    $applications = $model->applications(array('condition'=>'respondent_id IN (0'.implode(',',array_keys($respondents)).')'));
                    array_push($arrAudience, array('audience'=>$audience, 'respondents'=>$respondents, 'applications'=>$applications));
                }

                foreach ($model->groupsQuestions as $g => $group) {
                    foreach ($group->questions as $q => $question) {
                        if($question['type'] != Question::TYPE_OPEN && $question['type'] != Question::TYPE_ANSWPHOTO) {
                            $arrStat = array('question'=>$question);
                            //$arrStat['answers'] = array();
                            foreach ($arrAudience as $aud => $audience) {
                                if($audience['applications']){
                                    $arrStat['answers'][$aud] = $question->getStatsAnswers('qa.application_id IN ('.implode(',',array_keys($audience['applications'])).')');
                                    if(array_filter($arrStat['answers'][$aud]) && $question['type'] != Question::TYPE_SCALE_SCORE){
                                        $arrStat['answers'][$aud]['chart'] = Utils::chart(array_values($arrStat['answers'][$aud]), Answer::legendAnswers(array_keys($arrStat['answers'][$aud])), $question['id'].'_'.$aud);
                                        $arrStat['answers'][$aud]['pie'] = Utils::chart(array_values($arrStat['answers'][$aud]), Answer::legendAnswers(array_keys($arrStat['answers'][$aud])), $question['id'].'_'.$aud, 1);
                                    	// для вывода Google Chart формируем название для div
                                    	$arrStat['answers'][$aud]['group'] = Str_Replace(Array("{", "}", "-"), "D", $question['id'].'_'.$aud);

                                    }
                                }
                            }
                            $arrStat['answers'][$aud + 1] = $question->getStatsAnswers();
                            if(array_filter($arrStat['answers'][$aud + 1]) && $question['type'] != Question::TYPE_SCALE_SCORE){
                                //$arrStat['answers'][$aud + 1]['chart'] = Utils::chart(array_values($arrStat['answers'][$aud + 1]), Answer::legendAnswers(array_keys($arrStat['answers'][$aud + 1])), $question['id'].'_'.($aud + 1));
                                //$arrStat['answers'][$aud + 1]['pie'] = Utils::chart(array_values($arrStat['answers'][$aud + 1]), Answer::legendAnswers(array_keys($arrStat['answers'][$aud + 1])), $question['id'].'_'.($aud + 1), 1);
                            	// для вывода Google Chart формируем название для div
                             	$arrStat['answers'][$aud + 1]['group'] = Str_Replace(Array("{", "}", "-"), "D", $question['id'].'_'.($aud + 1));
                            }

                            array_push($resStat, $arrStat);
                        }
                        else if ($question['type'] == Question::TYPE_ANSWPHOTO) {
                        	$arrStat = array('question'=>$question);

				            $photos = Yii::app()->db->createCommand()
				                ->select('answer_text')
				                ->from('{{application_answers}}')
				                ->where('question_id=:question', array(':question'=>$question->id))
				                ->queryAll();

				            $answers = array();

							foreach($photos as $photo) {
								$answers[] = $photo['answer_text'];
							}

                        	$arrStat['answers'][0] = $answers;

                        	array_push($resStat, $arrStat);
                        }
                    }
                }
            }
            $this->render('statistics',array(
			'model'=>$model,
			'questions'=>$resStat,
                'id'=>$id,
		));
        }

        /*
	 * Export a particular model to CSV file.
	 */
        public function actionExport($id) {
         	//header('Content-Type: text/csv; charset=windows-1251; Content-Disposition: attachment; filename*="Na%C3%AFve%20file.txt"');

            $model = $this->loadModel($id);
            $arrayForExport = ExportFiles::getArrayForExport($id);
            $questions_arr = $arrayForExport['questions_arr'];
            $respondents = $arrayForExport['respondents'];
            $content = Utils::getFullRespondentData($questions_arr, $respondents);//получаем все как форматированный текст
            $filename = Yii::t('app', 'Quiz').'_'.$model->title.'_'.date('d_m_Y_H_i_s').'.csv';
            $content = '"'.Utils::intoCharset($model->title, "UTF-8", "windows-1251").'";' . "\n\n" . $content;
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
            //exit();
        }


    /*
     * Return count respondents
     */
    public function actionRespondents(){
        if(Yii::app()->request->isAjaxRequest){
            $target = new TargetAudience;
            $target->attributes = $_GET['TargetAudience'];

            if(isset($_GET['TargetAudience']['educations'])){
                $arrayEducation = array();
                foreach($_GET['TargetAudience']['educations'] as $i=>$education)
                        $arrayEducation[$education] = DictEducation::model()->findByPk($education);
                $target->educations = $arrayEducation;
            }

            if(isset($_GET['TargetAudience']['scopes'])){
                $arrayScopes = array();
                foreach($_GET['TargetAudience']['scopes'] as $i=>$scope)
                    $arrayScopes[$scope] = DictScope::model()->findByPk($scope);
                $target->scopes = $arrayScopes;
            }

            if(isset($_GET['TargetAudience']['job_position'])){
                $arrayJobPosition = array();
                foreach($_GET['TargetAudience']['job_position'] as $i=>$position)
                    $arrayJobPosition[$position] = DictJobPosition::model()->findByPk($position);
                $target->job_position = $arrayJobPosition;
            }

            if(isset($_GET['TargetAudience']['countries'])){
                $arrayCountries = array();
                foreach($_GET['TargetAudience']['countries'] as $i=>$country)
                    $arrayCountries[$country] = DictCountry::model()->findByPk($country);
                $target->countries = $arrayCountries;
            }

            if(isset($_GET['TargetAudience']['cities'])){
                $arrayCities = array();
                foreach($_GET['TargetAudience']['cities'] as $i=>$city)
                    $arrayCities[$city] = DictCity::model()->findByPk($city);
                $target->cities = $arrayCities;
            }

            if(isset($_GET['TargetAudience']['classfAnswers'])){
                $arrayClassfAnswers = array();
                foreach($_GET['TargetAudience']['classfAnswers'] as $i=>$canswer)
                    $arrayClassfAnswers[$canswer] = Answer::model()->findByPk($canswer);
                $target->classfAnswers = $arrayClassfAnswers;
            }

            if(isset($_GET['TargetAudience']['groupsRespondents'])){
                $arrayGroupsRespondents = array();
                foreach($_GET['TargetAudience']['groupsRespondents'] as $i=>$group)
                    $arrayGroupsRespondents[$group] = GroupRespondents::model()->findByPk($group);
                $target->groupsRespondents = $arrayGroupsRespondents;
            }


            $count = count($target->getRespondents());

            echo $count;

            die();
        }
    }

        /*
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Quiz::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/*
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='quiz-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionExcel($id){

        $model = $this->loadModel($id);
        // Создаем объект класса PHPExcel
        $xls = new PHPExcel();
// Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
// Получаем активный лист
        $sheet = $xls->getActiveSheet();
// Подписываем лист
        $sheet->mergeCells('A1:A3');
        $sheet->setTitle('Query Agent Quiz');
        $sheet->getStyle('A1')->getFill()->setFillType(
            PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setRGB(Branding::getTopColor());
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B2', 'Название опроса');

        $sheet->setCellValue('C2', $model->title);
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
            ));
        $sheet->getStyle('C3')->applyFromArray($styleArray);

        $imagePath = ".".Branding::getLogo();
        $img = getimagesize($imagePath);
        if (file_exists($imagePath)) {
            $logo = new PHPExcel_Worksheet_Drawing();
            $logo->setPath($imagePath);
//            $logo->setCoordinates("A1");
//            $logo->setOffsetX(1);
//            $sheet->getColumnDimension('A')->setWidth(20);
//            $logo->setOffsetY(5);
            $logo->setWorksheet($sheet);
        }
        else $sheet->setCellValue('A1', 'No logo');

        $arrayForExport = ExportFiles::getArrayForExport($id);
        $questions_arr = $arrayForExport['questions_arr'];
        $i = 0;
        foreach($questions_arr as $item)
        {
            $sheet->setCellValueByColumnAndRow($i,5,$item);
            ++$i;
        }
        $respondents = $arrayForExport['respondents'];

        $j = 6;
        foreach($respondents as $app)
        {
            $i = 0;
            foreach($app as $item)
            {
                $sheet->setCellValueByColumnAndRow($i,$j,$item);
                ++$i;
            }
            ++$j;

        }
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet
                ->getColumnDimension($col)
                ->setWidth(30);
        }
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getStyle($col.'5')
                ->getAlignment()->setWrapText(true);
            $sheet->getStyle($col.'5')->applyFromArray($styleArray);
        }

        $sheet->setAutoFilter('A5:'.$col.($j-1));
        $sheet->getStyle('A6:'.$col.($j-1))
            ->getAlignment()->setWrapText(true);
        // Save a xls file
        $filename = Yii::t('app', 'Quiz').'_'.$model->title.'_'.date('d_m_Y_H_i_s');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');

        $objWriter->save('php://output');
        unset($this->objWriter);
        unset($this->objWorksheet);
        unset($this->objReader);
        unset($this->objPHPExcel);
        exit();
    }

    public function actionPdf()
    {
        $svg_arr = $_POST['charts'];
        $q_arr = $_POST['q_text'];
        $title = $_POST['title'];
        //print_r($svg_arr);
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="filename.pdf";');
        header('Cache-Control: max-age=0');
        $mpdf = Yii::app()->ePdf->mpdf('utf-8', 'A4');
        $stylesheet = file_get_contents('/css/starbox.css');

        $mpdf->SetHTMLHeader('<table><tr><td><div style="background: '.Branding::getTopColor().'; width: 100px;padding:3px;float:left;"><img src="'.Branding::getLogo().'"></div></td>
        <td><span style="margin-left:10px; font-weight:bold;"><i>'.$title.'</i></span></td></tr></table>');
        $mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: middle; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;"><tr>
<td width="33%"><span style="font-weight: bold; font-style: italic;">{DATE j.m.Y}</span></td>
<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
<td width="33%" style="text-align: right;"><div><img src="/upload/branding/logo.png" width="80"></div></td>
</tr></table>
');
        $mpdf->WriteHTML('<progress value="88" max="100">88%</progress>');
        //$mpdf->WriteHTML($html);
        $mpdf->WriteHTML("<br>");
        $mpdf->WriteHTML("<br>");
        //$mpdf->WriteHTML("<h2>".$title."</h2>");
        for ($i = 0, $k = 0;$i < sizeof($q_arr); ++$i, ++$k){
            if ($k%2 == 0 && $k != 0) {
                $mpdf->AddPage();
                $mpdf->WriteHTML("<br><br>");
            }
            $mpdf->WriteHTML($q_arr[$i]);
            foreach($svg_arr[$i] as $svg){
                $mpdf->WriteHTML($svg);
            }
            $mpdf->WriteHTML("<br><br>");
        }
        $mpdf->Output();
        exit();
    }
}
