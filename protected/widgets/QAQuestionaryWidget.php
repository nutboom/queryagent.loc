<?php
/**
* QAQuestionaryWidget class file.
*
* Простой виджет для вывода опроса с ответами пользователя
*
*/
class QAQuestionaryWidget extends CWidget
{
    /**
* application - анкета, объект Application
* @var Application
*/
    public $application = null;

    public function init()
    {
        if(!$this->application)
            throw new CHttpException('500','Please set models Application properties for QAQuestionaryWidget!');


        $quizM = $this->application->quiz;
        $questionsArray = array();

        foreach ($quizM->groupsQuestions as $g => $group) {
            $quuestionsGroup = $group->questions;
            foreach ($quuestionsGroup as $q => $question) {
                $quuestionsGroup[$q]->respondentAnswer = null;
                $answersApp = ApplicationAnswer::model()->findAll('application_id=:application AND question_id=:question', array(':application'=>$this->application['id'],':question'=>$question['id']));
                if($answersApp){
                    $arrAnsw = array();
                    foreach ($answersApp as $a => $answer) {
                        if($answersApp[$a]->answer)
                            array_push ($arrAnsw, $answersApp[$a]->answer);
                        else
                            array_push ($arrAnsw, $answersApp[$a]);
                    }
                    $quuestionsGroup[$q]->respondentAnswer = $arrAnsw;
                }
            }
            $index = -1;

            if($group['id'] == $this->application['check_question_group_id']){
                if(count($quuestionsGroup) < $this->application['check_question_order'])
                    $index = count($quuestionsGroup);
                else
                    $index = $this->application['check_question_order'] == 0 ? $this->application['check_question_order'] : $this->application['check_question_order'] - 1;

                $this->application->checkQuestion->respondentAnswer = array($this->application->checkAnswer);
                $quuestionsGroup = array_merge(array_slice($quuestionsGroup, 0, $index), array($this->application->checkQuestion), array_slice($quuestionsGroup, $index));
            }
            $questionsArray = array_merge($questionsArray, $quuestionsGroup);
        }

        CWidget::render('qa_questionary', array('questionsArray'=>$questionsArray));
    }
}

?>