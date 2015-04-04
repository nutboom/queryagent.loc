<?php
class TemplatesController extends Controller {
    public $layout='//layouts/column2';


    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules() {
        return array(
            array('allow', 'actions'=>array('index','create','update','delete','conditionsDisplay','saveImg','deleteElement','setOrderBy'), 'users'=>Yii::app()->getModule('user')->getManagers(),),
            array('deny', 'users'=>array('*'),),
        );
    }

    public function actionIndex() {
        $criteria=new CDbCriteria;
        #$criteria->addInCondition('manager_id', array(0,Yii::app()->user->id)); 
        $criteria->compare('manager_id', Yii::app()->user->id); 
        $criteria->compare('is_deleted', Templates::NO_DELETED); 
        
        $dataProvider = new CActiveDataProvider('Templates', array('criteria'=>$criteria,));
        
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionCreate() {
        $model = new Templates;
        
        $model->manager_id = Yii::app()->user->id;

        if(isset($_POST['Templates'])) {
            $model->attributes = $_POST['Templates'];

            if ($model->save()) {
                $this->redirect(array("/catalog/templates/update/id/".$model->id));
            }
        }

        $this->render('create',array(
            'model'=>$model
        ));
    }

    public function actionUpdate($id) {
        $quizEl = $this->loadModelQuiz($id);

        if (count($quizEl->groupsQuestions) > 0) {
            $groups = $quizEl->groupsQuestions;
        }
        else {
            $groups = TemplatesGroupQuestions::EmptyInit();
        }

        if(isset($_POST['Templates'])) {
            $quizEl->attributes = $_POST['Templates'];

            if ($quizEl->save()) {
            }
        }

        if (isset($_POST['GroupQuestions'])) {
            $valid = true;
            //$indexGroupQuestions = 1;
            foreach ($_POST['GroupQuestions'] as $g => $group) {
                if ($group['id']) {
                    $model = TemplatesGroupQuestions::model()->findByPk($group['id']);
                }
                else {
                    $model = new TemplatesGroupQuestions;
                }

                if ($model->orderby == 1) {
                    $model->clearGroupQuestionsConnectionWithAnswer();
                }

                $model->attributes = $group;
                $model->template_id = $id;
                $model->orderby = $group['groups_orderby'];

                if (isset ($group['conditions']) && $group['conditions']['view'] == 1) {
                    foreach ($group['conditions'] as $question_id => $arrAnswer) {
                        if($question_id == 'view') {
                            $model->clearGroupQuestionsConnectionWithAnswer();
                        }
                        else {
                            $questionC = TemplatesQuestion::model()->findByPk($question_id);
                            if ($questionC['type'] == TemplatesQuestion::TYPE_CLOSE){
                                $arrAnswersConditions = array();
                                foreach ($arrAnswer as $a => $answer){
                                    $answerModel = TemplatesAnswers::model()->findByPk($answer);
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
                    if (isset($question['id'])) {
                        if ($question['id']) {
                            $modelQuestion = TemplatesQuestion::model()->findByPk($question['id']);
                        }
                        else {
                            $modelQuestion = new TemplatesQuestion;
                        }

                        $modelQuestion->attributes = $question;

                        $modelQuestion->orderby = $question['question_orderby'];

                        if (isset($question['image']) && count($question['image']) > 0) {
                            $arrImagesForQuestion = array();
                            foreach ($question['image'] as $i => $image) {
                                if ($image['link']) {
                                    if ($image['id']) {
                                        $modelImage = TemplatesQuestionMedia::model()->findByPk($image['id']);
                                    }
                                    else {
                                        $modelImage = new TemplatesQuestionMedia;
                                    }

                                    $modelImage->attributes = $image;
                                    $modelImage->link = $image['link'];
                                    $modelImage->image = CUploadedFile::getInstanceByName('GroupQuestions['.$g.'][Question]['.$q.'][image]['.$i.'][image]');
                                    array_push($arrImagesForQuestion, $modelImage);
                                }
                            }
                            $modelQuestion->pictures = $arrImagesForQuestion;
                        }

                        if (
                            $question['type'] != TemplatesQuestion::TYPE_OPEN &&
                            $question['type'] != Question::TYPE_ANSWPHOTO &&
                            $question['type'] != Question::TYPE_SCALE_SCORE &&
                            (isset($question['answer']) && count($question['answer']) > 0)
                        ) {
                            $arrAnswersForQuestion = array();
                            //$indexAnswer = 1;
                            foreach ($question['answer'] as $a => $answer) {
                                if ($answer['id']) {
                                    $modelAnswer = TemplatesAnswers::model()->findByPk($answer['id']);
                                }
                                else {
                                    $modelAnswer = new TemplatesAnswers;
                                }

                                $modelAnswer->attributes = $answer;
                                
                                if ($question['type'] != TemplatesQuestion::TYPE_SCALE_CLOSE) {
                                    $modelAnswer->orderby = $answer['answer_orderby'];
                                }

                                array_push($arrAnswersForQuestion, $modelAnswer);
                            }
                            $modelQuestion->answers = $arrAnswersForQuestion;
                        }

                        array_push($arrQuestionsForGroup, $modelQuestion);
                    }
                }

                $model->questions = $arrQuestionsForGroup;
                $groups[$g - 1] = $model;

                if (!$model->withRelated->save(true, array('questions' => array('pictures','answers',),'answers',))) {
                    $valid = false;
                    break;
                }
            }

            if (!Yii::app()->request->isAjaxRequest) {
                if ($valid) {
                    $this->refresh();
                }
            }
        }

        $stQ = new StructureQuiz;

        if (Yii::app()->request->isAjaxRequest) {
            $structure = $stQ->getStructureQuiz($groups);
            if($structure->errors) {
                $data = $groups;
            }
            else {
                $data = TemplatesGroupQuestions::model()->with('questions','questions.pictures','questions.answers')->findAll('template_id = :quiz' , array('quiz' => $id));
                $emptyEl = TemplatesGroupQuestions::EmptyInit();
                foreach ($emptyEl as $e => $group) {
                    array_push($data, $group);
                }
            }

            print_r(StructureQuiz::jsonStructure($data, $structure->errors));

            Yii::app()->end();
        }
        else {
            $this->render('update',array(
                'model' => StructureQuiz::jsonStructure($groups),
                'groups' => $groups,
                'question' => $stQ->getStructureQuiz($groups),
                'quiz' => $quizEl,
            ));
        }
    }

    /**
     * Deletes a particular model question picture.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDeleteElement($quiz) {
        if(Yii::app()->request->isPostRequest) {
            if ($_POST['id']) {
                switch ($_POST['dataName']) {
                    case 'groups':
                        TemplatesGroupQuestions::model()->findByPk($_POST['id'])->delete();
                        break;
                    case 'question':
                        TemplatesQuestion::model()->findByPk($_POST['id'])->delete();
                        break;
                    case 'answer':
                        TemplatesAnswers::model()->findByPk($_POST['id'])->delete();
                        break;
                    case 'picture':
                        TemplatesQuestionMedia::model()->findByPk($_POST['id'])->delete();
                        break;
                }
            }
            else {
                if ($_POST['dataName'] == 'picture'){
                    if ($_POST['src']) {
                        $imagePath=Yii::getPathOfAlias('webroot').$_POST['src'];
                        if (is_file($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }
            }

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax'])) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/catalog/templates/update/id/'.$quiz));
            }
        }
        else {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionConditionsDisplay($quiz, $id) {
            if(Yii::app()->request->isAjaxRequest) {
                $quizM = $this->loadModelQuiz($quiz);
                $curGroup = TemplatesGroupQuestions::model()->find('orderby = :order AND template_id=:quiz',array(':order'=>$id, ':quiz'=>$quiz));
                if(!$curGroup)
                    $curGroup = new TemplatesGroupQuestions;

                $criteria=new CDbCriteria;
                $criteria->condition = 'orderby < :order AND template_id = :quiz';
                $criteria->params = array(':order' => $id, ':template_id'=>$quiz);
                $criteria->order = 'orderby';
                $groups = TemplatesGroupQuestions::model()->findAll($criteria);

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
            if (isset($_FILES['GroupQuestions'])) {
                foreach ($_FILES['GroupQuestions']['name'] as $g => $group) {
                    foreach ($group['Question'] as $q => $question) {
                        foreach ($question['image'] as $i => $image) {
                            $imageU = CUploadedFile::getInstanceByName('GroupQuestions['.$g.'][Question]['.$q.'][image]['.$i.'][image]');
                            $ext = $imageU->getExtensionName();
                            $name = date('dmy').time().rand().$g.$q.$i.'.'.$ext;
                            TemplatesQuestionMedia::saveImage($imageU, Yii::getPathOfAlias('webroot').TemplatesQuestionMedia::getPath().$name);
                        }
                    }
                }
            }
            echo "{";
            echo                "id: 'GroupQuestions_".$g."_Question_".$q."_image_".$i."_image',\n";
            echo                "linkId: 'GroupQuestions_".$g."_Question_".$q."_image_".$i."_link',\n";
            echo                "name: '" . $name . "',\n";
            echo                "msg: '" . TemplatesQuestionMedia::getPath().$name . "'\n";
            echo "}";
        }

        public function actionSetOrderBy($quiz){
            if(Yii::app()->request->isPostRequest) {
                    // we only allow deletion via POST request
                    switch ($_POST['nameOrder']) {
                        case 'groups':
                            $model = TemplatesGroupQuestions::model();
                            break;
                        case 'question':
                            $model = TemplatesQuestion::model();
                            break;
                        case 'answer':
                            $model = TemplatesAnswers::model();
                            break;
                        /*case 'picture':
                            $model = TemplatesQuestionMedia::model();
                            break;*/
                    }

                    foreach ($_POST['account'] as $a => $elem){
                        $model->updateByPk($elem['id'], array('orderby'=>$elem['orderby']));
                    }

                    exit();

                    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                    if(!isset($_GET['ajax']))
                            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/catalog/templates/update/id/'.$quiz));
            } else
                throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }

    public function loadModelQuiz($template_id) {
        $template = Templates::model()->with('groupsQuestions','groupsQuestions.questions','groupsQuestions.questions.answers','groupsQuestions.questions.pictures')->findByPk($template_id);
        if ($template === null) {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        
        return $template;
    }

        /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if(isset($_POST['ajax']) && $_POST['ajax']==='structure-quiz-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionDelete($id) {
        $quiz = $this->loadModelQuiz($id);
        $quiz->is_deleted = 1;
        $quiz->save();

    }
}