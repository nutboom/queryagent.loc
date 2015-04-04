<?php
/*
 * Скрипт отправляет смс тем у кого не подтвержден телефон
 */
    class SenderCommand extends CConsoleCommand {
        public function run($args) {
            $users = Respondent::model()->findAll("phone_is_confirmed=0 AND (blocked IS NULL OR blocked=0)");
            foreach($users as $respondent){
                if($respondent['phone_number'] && $respondent['phone_code']){
                    Utils::send_sms($respondent['phone_number'], Yii::t('app', 'Confirmation code phone').': '.$respondent['phone_code']);
                    sleep(5);
                }
            }
        }
    }
?>
