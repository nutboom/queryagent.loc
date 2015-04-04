<?php
class ExportFiles{

    public static function getArrayForExport($id)//получает все вопросы\ответы респондентов
    {
        //Yii::import('ext.CSVExport.ECSVExport');
        $sql = "SELECT id FROM tbl_group_questions WHERE quiz_id = :quiz_id";
        $sql2 = "SELECT * FROM tbl_questions WHERE group_id IN (".$sql.") ORDER BY orderby ASC";
        $questions = Yii::app()->db->createCommand($sql2)->queryAll(true, array(":quiz_id"=>$id));
        $questions_arr = array();
        $questions_arr[0] = "Респондент";
        foreach($questions as $question)
        {
            $questions_arr[$question['id']] = $question['text'];
        }
        $sql = "SELECT * FROM tbl_applications WHERE quiz_id = :quiz_id AND state='close'";
        $applications = Yii::app()->db->createCommand($sql)->queryAll(true, array(":quiz_id"=>$id));

        $respondents = array();
        foreach($applications as $key => $application)
        {
            $sql = "SELECT tb1.question_id, tb1.answer_id, tb1.answer_text as open_answer, tb2.text as question_text, tb3.text as answer_text
            FROM tbl_application_answers tb1
            LEFT JOIN tbl_questions tb2 ON (tb1.question_id = tb2.id)
            LEFT JOIN tbl_answers tb3 ON (tb1.answer_id = tb3.id)
            WHERE tb1.application_id = :app_id";
            $answers = Yii::app()->db->createCommand($sql)->queryAll(true, array(":app_id"=>$application['id']));
            $answers = Utils::getUniqueSubArrays($answers);
            $sql = "SELECT CONCAT_WS(' ', first_name, last_name, phone_number) as respondent_name FROM tbl_respondents WHERE id = :id";
            $respondent_name = Yii::app()->db->createCommand($sql)->queryAll(true, array(":id"=>$application['respondent_id']));
            $respondents[$key][0] = ($respondent_name[0]['respondent_name'])?$respondent_name[0]['respondent_name']:Yii::t('app', "Anonymus respondentus");
            foreach ($questions_arr as $id => $question)
            {
                if ($id != '0')
                    $respondents[$key][] = Utils::getAnswer($id, $answers);
            }

        }

        $result['questions_arr'] = $questions_arr;
        $result['respondents'] = $respondents;
        return $result;
    }
}