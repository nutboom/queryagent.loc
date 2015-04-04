<?php

class StructureQuizController extends Controller
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
	public function accessRules()
	{
		return array(
                        array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','conditionsDisplay','saveImg','deleteElement','setOrderBy'),
				'users'=>Yii::app()->getModule('user')->getManagers(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionIndex($quiz) {
        #print_r($_POST);
        #exit;

            /*echo '<pre>';
            print_r($_POST);
            print_r($_FILES);
            exit();*/

            /*$fopen = fopen("1.txt", a);
            fputs($fopen, print_r($_POST, true));
            fclose($fopen);*/

            $quizEl = $this->loadModelQuiz($quiz);

            if(count($quizEl->groupsQuestions) > 0)
                $groups = $quizEl->groupsQuestions;
            else
                $groups = GroupQuestions::EmptyInit();

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);


            if (isset($_POST['template_id'])) {
                $template = Templates::model()->findByPk($_POST['template_id']);
                if ($template === null) {
                    throw new CHttpException(404,'The requested page does not exist.');
                }

                $connection=Yii::app()->db;

                /*# группы вопросов
                $gq_sql     = $connection->createCommand("SELECT * FROM `tbl_group_questions` WHERE `quiz_id` = '".$quizEl->quiz_id."'");
                $gq_data    = $gq_sql->query();
                while(($group=$gq_data->read())!==false) {
                    $lgqa_sql = $connection->createCommand("SELECT * FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$group['id']."'");
                    $lgqa_data = $lgqa_sql->query();
                    while(($lgqa=$lgqa_data->read())!==false) {
                        $connection->prepare("DELETE FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$lgqa['group_questions_id']."' AND `answers_id` = '".$lgqa['answers_id']."'")->execute();
                    }

                    # удаляем вопросы
                    $que_sql = $connection->createCommand("SELECT * FROM `tbl_questions` WHERE `group_id` = '".$group['id']."'");
                    $que_data = $que_sql->query();
                    while(($question=$que_data->read())!==false) {
                        # удаляем ответы из вопроса
                        $connection->createCommand("DELETE FROM `tbl_answers` WHERE `question_id` = '".$question['id']."'")->execute();

                        # удаляем медиа из вопроса
                        $connection->createCommand("DELETE FROM `tbl_question_media` WHERE `question_id` = '".$question['id']."'")->execute();

                        # Удаляем сам вопрос
                        $connection->createCommand("DELETE FROM `tbl_questions` WHERE `id` = '".$question['id']."'")->execute();
                    }

                    # удаляем эту группу вопросов
                    $connection->createCommand("DELETE FROM `tbl_group_questions` WHERE `quiz_id` = '".$quiz['quiz_id']."'")->execute();
                }*/

                // copys group of questions
                $arrayAnswers   =   Array();
                $arrayQuestions =   Array();
                $command=$connection->createCommand("SELECT * FROM `tbl_templates_group_questions` WHERE `template_id` = '".$template->id."'");
                $groupsc = $command->query();
                while(($group=$groupsc->read())!==false) {
                    // save group of questions
                    $condition  =   ($group['condition_question_id']) ? "'".$arrayQuestions[$group['condition_question_id']]."'" : "null";
                    $connection->createCommand("
                        INSERT INTO `tbl_group_questions` SET
                            `quiz_id`               =   '".$quizEl->quiz_id."',
                            `condition_question_id` =   ".$condition.",
                            `orderby`               =   '".$group['orderby']."'
                    ")->execute();
                    $idGroup    =   $connection->lastInsertID;

                    // copys link in group questions at answer
                    $command=$connection->createCommand("SELECT * FROM `tbl_templates_link_group_questions_answers` WHERE `group_questions_id` = '".$group['id']."'");
                    $links = $command->query();
                    while(($link=$links->read())!==false) {
                        // save link
                        $connection->createCommand("
                            INSERT INTO `tbl_link_group_questions_answers` SET
                                `group_questions_id`    =   '".$idGroup."',
                                `answers_id`            =   '".$arrayAnswers[$link['answers_id']]."'
                        ")->execute();
                    }

                    // copys questions
                    $command=$connection->createCommand("SELECT * FROM `tbl_templates_questions` WHERE `group_id` = '".$group['id']."'");
                    $questions = $command->query();
                    while(($question=$questions->read())!==false) {
                        // save question
                        $questionHash   =   $this->genHash();
                        $connection->createCommand("
                            INSERT INTO `tbl_questions` SET
                                `id`            =   '".$questionHash."',
                                `group_id`      =   '".$idGroup."',
                                `text`          =   '".$question['text']."',
                                `type`          =   '".$question['type']."',
                                `orderby`       =   '".$question['orderby']."',
                                `scaled_size`   =   '".$question['scaled_size']."',
                                `is_class`      =   '".$question['is_class']."'
                        ")->execute();

                        // save dumper question
                        $arrayQuestions[$question['id']] = $questionHash;

                        // copys answers
                        $command=$connection->createCommand("SELECT * FROM `tbl_templates_answers` WHERE `question_id` = '".$question['id']."'");
                        $answers = $command->query();
                        while(($answer=$answers->read())!==false) {
                            // save answer
                            $answerHash =   $this->genHash();
                            $connection->createCommand("
                                INSERT INTO `tbl_answers` SET
                                    `id`            =   '".$answerHash."',
                                    `question_id`   =   '".$questionHash."',
                                    `text`          =   '".$answer['text']."',
                                    `orderby`       =   '".$answer['orderby']."'
                            ")->execute();

                            // save dumper answers
                            $arrayAnswers[$answer['id']] = $answerHash;
                        }

                        // copys quesion media
                        $command=$connection->createCommand("SELECT * FROM `tbl_templates_question_media` WHERE `question_id` = '".$question['id']."'");
                        $medias = $command->query();
                        while(($media=$medias->read())!==false) {
                            // save media
                            if (preg_match("/^.+\.(png|gif|jpg|jpeg)$/i", $media['link']))
                            {
                                $connection->createCommand("
                                INSERT INTO `tbl_question_media` SET
                                    `question_id`   =   '".$questionHash."',
                                    `link`          =   '".$media['link']."'
                            ")->execute();
                            }


                        }
                    }
                }
            
                Yii::app()->user->setFlash('success', Yii::t('app', 'Quiz template done'));
            }

            if(isset($_POST['GroupQuestions']))
            {
                //GroupQuestions::model()->deleteAll('quiz_id = :quiz' , array('quiz' => $quiz));
                $valid = true;
                //$indexGroupQuestions = 1;
                foreach ($_POST['GroupQuestions'] as $g => $group) {
                    if($group['id'])
                        $model = GroupQuestions::model()->findByPk($group['id']);
                    else
                        $model = new GroupQuestions;

                    if($model->orderby == 1)
                        $model->clearGroupQuestionsConnectionWithAnswer ();
                    $model->attributes = $group;
                    $model->quiz_id = $quiz;
                    $model->orderby = $group['groups_orderby'];

                    if(isset ($group['conditions']) && $group['conditions']['view'] == 1) {
                        foreach ($group['conditions'] as $question_id => $arrAnswer){
                            if($question_id == 'view'){
                                $model->clearGroupQuestionsConnectionWithAnswer();
                            } else {
                                $questionC = Question::model()->findByPk($question_id);
                                if($questionC['type'] == Question::TYPE_CLOSE){
                                    $arrAnswersConditions = array();
                                    foreach ($arrAnswer as $a => $answer){
                                        $answerModel = Answer::model()->findByPk($answer);
                                        array_push($arrAnswersConditions, $answerModel);
                                    }
                                    $model->answers = $arrAnswersConditions;
                                    $model->condition_question_id = $question_id;
                                }
                            }
                        }
                    }

                    $arrQuestionsForGroup = array();

                    //$indexQuestion = 1;
                    foreach ($group['Question'] as $q => $question) {
                        if(isset($question['id'])){
                            if($question['id'])
                                $modelQuestion = Question::model()->findByPk($question['id']);
                            else
                                $modelQuestion = new Question;

                            $modelQuestion->attributes = $question;

                            $modelQuestion->orderby = $question['question_orderby'];
                            $modelQuestion->is_class = 0;

                            if(isset($question['image']) && count($question['image']) > 0){
                                $arrImagesForQuestion = array();
                                foreach ($question['image'] as $i => $image) {
                                    if($image['link'] && preg_match("/^.+\.(png|gif|jpg|jpeg)$/i", $image['link'])){
                                        if($image['id'])
                                            $modelImage = QuestionMedia::model()->findByPk($image['id']);
                                        else
                                            $modelImage = new QuestionMedia;
                                        $modelImage->attributes = $image;
                                        $modelImage->link = $image['link'];
                                        $modelImage->image = CUploadedFile::getInstanceByName('GroupQuestions['.$g.'][Question]['.$q.'][image]['.$i.'][image]');
                                        array_push($arrImagesForQuestion, $modelImage);
                                    }
                                }
                                $modelQuestion->pictures = $arrImagesForQuestion;
                            }

                            if($question['type'] != Question::TYPE_OPEN && $question['type'] != Question::TYPE_ANSWPHOTO && $question['type'] != Question::TYPE_SCALE_SCORE && (isset($question['answer']) && count($question['answer']) > 0)){
                                $arrAnswersForQuestion = array();
                                //$indexAnswer = 1;
                                foreach ($question['answer'] as $a => $answer) {
                                    if($answer['id'])
                                        $modelAnswer = Answer::model()->findByPk($answer['id']);
                                    else
                                        $modelAnswer = new Answer;
                                    $modelAnswer->attributes = $answer;
                                    if($question['type'] != Question::TYPE_SCALE_CLOSE)
                                            $modelAnswer->orderby = $answer['answer_orderby'];
                                    array_push($arrAnswersForQuestion, $modelAnswer);
                                }
                                $modelQuestion->answers = $arrAnswersForQuestion;
                            }
                            array_push($arrQuestionsForGroup, $modelQuestion);
                        }
                    }
                    $model->questions = $arrQuestionsForGroup;
                    $groups[$g - 1] = $model;

                    if(!$model->withRelated->save(true,array(
                        'questions'=>array(
                            'pictures',
                            'answers',
                        ),
                        'answers',
                    ))) {
                            $valid = false;
                            break;
                    }
                }
                //exit();

                if(!Yii::app()->request->isAjaxRequest)
                    if($valid) {
                        if (isset($_POST['saveAndRedirect'])) {
                     	   $this->redirect(array('/'.$quizEl->type.'/'.$quizEl->quiz_id.'/targetAudience'));
                        }
                        else {
                       		$this->refresh();
                        }
                    }
                        //$this->refresh();
            }


            if(isset($_POST['Templates'])) {
                $template = new Templates;
                $template->title = $_POST['Templates']['title'];
                $template->manager_id = Yii::app()->user->id;
                $template->type = $quizEl->type;

                if ($template->save()) {
                    $connection=Yii::app()->db;
                    // copys group of questions
                    $arrayAnswers   =   Array();
                    $arrayQuestions =   Array();
                    $command=$connection->createCommand("SELECT * FROM `tbl_group_questions` WHERE `quiz_id` = '".$quizEl->quiz_id."'");
                    $groupst = $command->query();
                    while(($group=$groupst->read())!==false) {
                        // save group of questions
                        $condition  =   ($group['condition_question_id']) ? "'".$arrayQuestions[$group['condition_question_id']]."'" : "null";
                        $connection->createCommand("
                            INSERT INTO `tbl_templates_group_questions` SET
                                `template_id`           =   '".$template->id."',
                                `condition_question_id` =   ".$condition.",
                                `orderby`               =   '".$group['orderby']."'
                        ")->execute();
                        $idGroup    =   $connection->lastInsertID;

                        // copys link in group questions at answer
                        $command=$connection->createCommand("SELECT * FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$group['id']."'");
                        $links = $command->query();
                        while(($link=$links->read())!==false) {
                            // save link
                            $connection->createCommand("
                                INSERT INTO `tbl_templates_link_group_questions_answers` SET
                                    `group_questions_id`    =   '".$idGroup."',
                                    `answers_id`            =   '".$arrayAnswers[$link['answers_id']]."'
                            ")->execute();
                        }

                        // copys questions
                        $command=$connection->createCommand("SELECT * FROM `tbl_questions` WHERE `group_id` = '".$group['id']."'");
                        $questions = $command->query();
                        while(($question=$questions->read())!==false) {
                            // save question
                            $questionHash   =   $this->genHash();
                            $connection->createCommand("
                                INSERT INTO `tbl_templates_questions` SET
                                    `id`            =   '".$questionHash."',
                                    `group_id`      =   '".$idGroup."',
                                    `text`          =   '".$question['text']."',
                                    `type`          =   '".$question['type']."',
                                    `orderby`       =   '".$question['orderby']."',
                                    `scaled_size`   =   '".$question['scaled_size']."',
                                    `is_class`      =   '".$question['is_class']."'
                            ")->execute();

                            // save dumper question
                            $arrayQuestions[$question['id']] = $questionHash;

                            // copys answers
                            $command=$connection->createCommand("SELECT * FROM `tbl_answers` WHERE `question_id` = '".$question['id']."'");
                            $answers = $command->query();
                            while(($answer=$answers->read())!==false) {
                                // save answer
                                $answerHash =   $this->genHash();
                                $connection->createCommand("
                                    INSERT INTO `tbl_templates_answers` SET
                                        `id`            =   '".$answerHash."',
                                        `question_id`   =   '".$questionHash."',
                                        `text`          =   '".$answer['text']."',
                                        `orderby`       =   '".$answer['orderby']."'
                                ")->execute();

                                // save dumper answers
                                $arrayAnswers[$answer['id']] = $answerHash;
                            }

                            // copys quesion media
                            $command=$connection->createCommand("SELECT * FROM `tbl_question_media` WHERE `question_id` = '".$question['id']."'");
                            $medias = $command->query();
                            while(($media=$medias->read())!==false) {
                                // save media
                                if (preg_match("/^.+\.(png|gif|jpg|jpeg)$/i", $media['link']))
                                {
                                    $connection->createCommand("
                                    INSERT INTO `tbl_templates_question_media` SET
                                        `question_id`   =   '".$questionHash."',
                                        `link`          =   '".$media['link']."'
                                ")->execute();
                                }


                            }
                        }
                    }

                    Yii::app()->user->setFlash('success', Yii::t('app', 'Quiz template successfuly saved'));
                }
            }


            $stQ = new StructureQuiz;

            //print_r(StructureQuiz::jsonStructure(GroupQuestions::model()->with('questions','questions.pictures','questions.answers')->findAll('quiz_id = :quiz' , array('quiz' => $quiz))));

            if(Yii::app()->request->isAjaxRequest) {
                $structure = $stQ->getStructureQuiz($groups);
                if($structure->errors)
                    $data = $groups;
                else{
                    $data = GroupQuestions::model()->with('questions','questions.pictures','questions.answers')->findAll('quiz_id = :quiz' , array('quiz' => $quiz));
                    $emptyEl = GroupQuestions::EmptyInit();
                    foreach ($emptyEl as $e => $group) {
                        array_push($data, $group);
                    }
                }

                print_r(StructureQuiz::jsonStructure($data, $structure->errors));

                Yii::app()->end();
            }else
                /*print_r (StructureQuiz::jsonStructure($groups));
                exit();*/
                $this->render('index',array(
                        'model' => StructureQuiz::jsonStructure($groups),
                        'groups' => $groups,
                        'question' => $stQ->getStructureQuiz($groups),
                        'quiz' => $quizEl,
                ));
	}

	/**
	 * Deletes a particular model question picture.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDeleteElement($quiz)
	{
                if(Yii::app()->request->isPostRequest)
		{
                    if($_POST['id']){
			// we only allow deletion via POST request
                        switch ($_POST['dataName']) {
                            case 'groups':
                                GroupQuestions::model()->findByPk($_POST['id'])->delete();
                                break;
                            case 'question':
                                Question::model()->findByPk($_POST['id'])->delete();
                                break;
                            case 'answer':
                                Answer::model()->findByPk($_POST['id'])->delete();
                                break;
                            case 'picture':
                                QuestionMedia::model()->findByPk($_POST['id'])->delete();
                                break;
                        }
                    } else {
                        if($_POST['dataName'] == 'picture'){
                            if($_POST['src']){
                                $imagePath=Yii::getPathOfAlias('webroot').$_POST['src'];
                                if(is_file($imagePath))
                                    unlink($imagePath);
                            }
                        }
                    }

                    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                    if(!isset($_GET['ajax']))
                            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/quiz/'.$quiz.'/StructureQuiz'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

        public function actionConditionsDisplay($quiz, $id) {
            if(Yii::app()->request->isAjaxRequest) {
                $quizM = $this->loadModelQuiz($quiz);
                $curGroup = GroupQuestions::model()->find('orderby = :order AND quiz_id=:quiz',array(':order'=>$id, ':quiz'=>$quiz));
                if(!$curGroup)
                    $curGroup = new GroupQuestions;

                $criteria=new CDbCriteria;
                $criteria->condition = 'orderby < :order AND quiz_id = :quiz';
                $criteria->params = array(':order' => $id, ':quiz'=>$quiz);
                $criteria->order = 'orderby';
                $groups = GroupQuestions::model()->findAll($criteria);

                $isCloseQuestions = false;
                foreach ($groups as $g => $group){
                    if ($group->closeQuestions){
                        $isCloseQuestions = TRUE;
                        break;
                    }
                }

                $this->renderPartial('_conditions', array('groups'=>$groups, 'curGroup'=>$curGroup, 'isCloseQuestions'=>$isCloseQuestions, 'quiz'=>$quiz, 'order'=>$id, 'answers'=>$curGroup->answers));

                exit();

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/quiz/'.$quiz.'/StructureQuiz'));
            } else
                throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }

        public function actionSaveImg($quiz) {
            if(isset($_FILES['GroupQuestions'])){
                foreach ($_FILES['GroupQuestions']['name'] as $g => $group) {
                    foreach ($group['Question'] as $q => $question) {
                        foreach ($question['image'] as $i => $image) {
                            $imageU = CUploadedFile::getInstanceByName('GroupQuestions['.$g.'][Question]['.$q.'][image]['.$i.'][image]');
                            $ext = $imageU->getExtensionName();
                            $name = date('dmy').time().rand().$g.$q.$i.'.'.$ext;
                            QuestionMedia::saveImage($imageU, Yii::getPathOfAlias('webroot').QuestionMedia::getPath().$name);
                        }
                    }
                }
            }
            echo "{";
            echo				"id: 'GroupQuestions_".$g."_Question_".$q."_image_".$i."_image',\n";
            echo				"linkId: 'GroupQuestions_".$g."_Question_".$q."_image_".$i."_link',\n";
            echo				"name: '" . $name . "',\n";
            echo				"msg: '" . QuestionMedia::getPath().$name . "'\n";
            echo "}";
        }

        public function actionSetOrderBy($quiz){
            if(Yii::app()->request->isPostRequest) {
                    // we only allow deletion via POST request
                    switch ($_POST['nameOrder']) {
                        case 'groups':
                            $model = GroupQuestions::model();
                            break;
                        case 'question':
                            $model = Question::model();
                            break;
                        case 'answer':
                            $model = Answer::model();
                            break;
                        /*case 'picture':
                            $model = QuestionMedia::model();
                            break;*/
                    }

                    foreach ($_POST['account'] as $a => $elem){
                        $model->updateByPk($elem['id'], array('orderby'=>$elem['orderby']));
                    }

                    exit();

                    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                    if(!isset($_GET['ajax']))
                            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/quiz/'.$quiz.'/StructureQuiz'));
            } else
                throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }

        public function loadModelQuiz($quiz_id)
	{
		$quiz = Quiz::model()->with('groupsQuestions','groupsQuestions.questions','groupsQuestions.questions.answers','groupsQuestions.questions.pictures')->findByPk($quiz_id);
		if($quiz===null)
			throw new CHttpException(404,'The requested page does not exist.');
                return $quiz;
        }

        /**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='target-audience-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    private function genHash() {
        $string =   "{".SubStr(Md5(Rand(1, Time())), 0, 8)."-";
        $string .=  SubStr(Md5(Rand(1, Time())), 0, 4)."-";
        $string .=  SubStr(Md5(Rand(1, Time())), 0, 4)."-";
        $string .=  SubStr(Md5(Rand(1, Time())), 0, 4)."-";
        $string .=  SubStr(Md5(Rand(1, Time())), 0, 12)."}";

        return strtoupper($string);
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}