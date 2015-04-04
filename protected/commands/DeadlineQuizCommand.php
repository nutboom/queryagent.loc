<?php
/*
 * Скрипт проверяется опросы с просроченными датами завершениями (deadline) и закрывает его
 */
    class DeadlineQuizCommand extends CConsoleCommand {
        public function run($args) {
            $criteria = new CDbCriteria;
            $criteria->compare('state', Quiz::STATE_WORK);
            $criteria->addCondition('DATE(deadline) < "'.date('Y-m-d').'"');

            $quizsDeadlineOut = Quiz::model()->findAll($criteria);
            foreach ($quizsDeadlineOut as $key => $value) {
                $value->state = Quiz::STATE_FILL;
                $value->save();
            }
        }
    }
?>
