<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class StructureQuiz extends CFormModel {
    /**
     * Get Json structure
     */

    public static function jsonStructure($groupsQuestions, $errors=null){

        $jsonData['groupsArray'] = array();
        foreach ($groupsQuestions as $g => $group) {
            $jsonData['groupsArray'][$g] = $group->attributes;
            if($group->attributes['id'])
                    $jsonData['groupsArray'][$g]['GroupQuestions[number_groups][id]'] = $group->attributes['id'];
            foreach ($group->questions as $q => $question) {
                if($question->attributes['id'])
                    $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][id]'] = $question->attributes['id'];
                $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][text]'] = $question->attributes['text'];
                $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][type]'] = $question->attributes['type'];
                $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][is_class]'] = $question->attributes['is_class'];
                $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][is_not_required]'] = $question->attributes['is_not_required'];
                $jsonData['groupsArray'][$g]['questionArray'][$q]['GroupQuestions[number_groups][Question][number_question][scaled_size]'] = $question->attributes['scaled_size'];

                foreach ($question->answers as $a => $answer){
                    if($answer->attributes['id'])
                        $jsonData['groupsArray'][$g]['questionArray'][$q]['answerArray'][array_search($a, array_keys($question->answers))]['GroupQuestions[number_groups][Question][number_question][answer][number_answer][id]'] = $answer->attributes['id'];
                    $jsonData['groupsArray'][$g]['questionArray'][$q]['answerArray'][array_search($a, array_keys($question->answers))]['GroupQuestions[number_groups][Question][number_question][answer][number_answer][text]'] = $answer->attributes['text'];
                    $jsonData['groupsArray'][$g]['questionArray'][$q]['answerArray'][array_search($a, array_keys($question->answers))]['GroupQuestions[number_groups][Question][number_question][answer][number_answer][orderby]'] = $answer->attributes['orderby'];
                }

                foreach ($question->pictures as $p => $picture){
                    if($picture->attributes['id'])
                        $jsonData['groupsArray'][$g]['questionArray'][$q]['pictureArray'][$p]['GroupQuestions[number_groups][Question][number_question][image][number_image][id]'] = $picture->attributes['id'];
                    $jsonData['groupsArray'][$g]['questionArray'][$q]['pictureArray'][$p]['GroupQuestions[number_groups][Question][number_question][image][number_image][link]'] = $picture->attributes['link'];
                    $jsonData['groupsArray'][$g]['questionArray'][$q]['pictureArray'][$p]['link'] = QuestionMedia::getPath().$picture->attributes['link'];
                }
            }
        }

        if(Yii::app()->request->isAjaxRequest){
            if($errors){
                $jsonText['errors'] = $errors;
            }
            $jsonText['content'] = $jsonData;
        } else
            $jsonText = $jsonData;


        return json_encode($jsonText);
    }

    public function getStructureQuiz($groupQuestions){
        $queestionTMP = $this;
        foreach ($groupQuestions as $g => $group) {
            $queestionTMP->addErrors($group->getErrors());
            //echo 'g='.$g." ";
           // print_r($group->getErrors());
            //echo "<hr>";
            foreach ($group->questions as $q => $question) {
                $q_errors = $question->getErrors();
                $q_errors_result = array();
                if(sizeof($q_errors)>0)
                {
                    foreach($q_errors['text'] as $q_err)
                    {
                        //$q_err = $q_err." Блок вопросов №".$g.". Вопрос №".($q+1);
                        $q_errors_result['text'][] = $q_err." <a href='#g".$g."q".$q."'>Блок вопросов ".($g+1)."-> Вопрос ".($q+1)."</a>";
                    }
                    foreach($q_errors['type'] as $q_err)
                    {
                        //$q_err = $q_err." Блок вопросов №".$g.". Вопрос №".($q+1);
                        $q_errors_result['type'][] = $q_err." <a href='#g".$g."q".$q."'>Блок вопросов ".($g+1)."-> Вопрос ".($q+1)."</a>";
                    }
                    foreach($q_errors['scaled_size'] as $q_err)
                    {
                        //$q_err = $q_err." Блок вопросов №".$g.". Вопрос №".($q+1);
                        $q_errors_result['scaled_size'][] = $q_err." <a href='#g".$g."q".$q."'>Блок вопросов ".($g+1)."-> Вопрос ".($q+1)."</a>";
                    }
                }

                //echo 'q='.$q." ";
                //print_r($question->getErrors());
                //echo "<hr>";
                $queestionTMP->addErrors($q_errors_result);
                foreach ($question->answers as $a => $answer)
                {
                    $queestionTMP->addErrors($answer->getErrors());
                }
                foreach ($question->pictures as $p => $picture)
                {
                    $queestionTMP->addErrors($picture->getErrors());
                }

            }
        }
        return $queestionTMP;
    }
}

?>
