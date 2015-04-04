<?php
exit;
header('Content-Type: text/html; charset=utf-8');

$config		=	require(dirname(__FILE__).'/protected/config/local/main.php');
$pdo		=	new PDO($config['db']['connectionString'], $config['db']['username'], $config['db']['password']);
$pdo->exec("set names utf8");



$not = array('Monly','vi_trend','russia2020','queryagent','shavlukevichgalina','dianit');

$u_sql = $pdo->query("SELECT * FROM `tbl_users` WHERE `username` NOT IN ('".implode("','", $not)."')");
while ($user = $u_sql->fetch(PDO::FETCH_ASSOC)) {
	# вытаскиваем опросы пользователей
	$q_sql = $pdo->query("SELECT * FROM `tbl_quiz` WHERE `manager_id` = '".$user['id']."'");
	while ($quiz = $q_sql->fetch(PDO::FETCH_ASSOC)) {
		#print_r($quiz);

			# удаляем анкеты
			$app_sql = $pdo->query("SELECT * FROM `tbl_applications` WHERE `quiz_id` = '".$quiz['quiz_id']."'");
			while ($application = $app_sql->fetch(PDO::FETCH_ASSOC)) {
				$pdo->prepare("DELETE FROM `tbl_application_answers` WHERE `application_id` = '".$application['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_application_comments` WHERE `application_id` = '".$application['id']."'")->execute();

				$pdo->prepare("DELETE FROM `tbl_applications` WHERE `id` = '".$application['id'])->execute();
			}

			# группы респондентов опросов
			$ta_sql = $pdo->query("SELECT * FROM `tbl_target_audience` WHERE `quiz_id` = '".$quiz['quiz_id']."'");
			while ($audience = $ta_sql->fetch(PDO::FETCH_ASSOC)) {
				$pdo->prepare("DELETE FROM `tbl_link_education_target_audience` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_city` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_classif_answers` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_country` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_group_respondents` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_job_position` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
				$pdo->prepare("DELETE FROM `tbl_link_target_audience_scope` WHERE `target_audience_id` = '".$audience['id']."'")->execute();
			}


			# группы вопросов
			$gq_sql = $pdo->query("SELECT * FROM `tbl_group_questions` WHERE `quiz_id` = '".$quiz['quiz_id']."'");
			while ($group = $gq_sql->fetch(PDO::FETCH_ASSOC)) {
				$lgqa_sql = $pdo->query("SELECT * FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$group['id']."'");
				while ($lgqa = $lgqa_sql->fetch(PDO::FETCH_ASSOC)) {
					$pdo->prepare("DELETE FROM `tbl_link_group_questions_answers` WHERE `group_questions_id` = '".$lgqa['group_questions_id']."' AND `answers_id` = '".$lgqa['answers_id']."'")->execute();
				}

				# удаляем вопросы
				$que_sql = $pdo->query("SELECT * FROM `tbl_questions` WHERE `group_id` = '".$group['id']."'");
				while ($question = $que_sql->fetch(PDO::FETCH_ASSOC)) {
					# удаляем ответы из вопроса
					$pdo->prepare("DELETE FROM `tbl_answers` WHERE `question_id` = '".$question['id']."'")->execute();

					# удаляем медиа из вопроса
					$pdo->prepare("DELETE FROM `tbl_question_media` WHERE `question_id` = '".$question['id']."'")->execute();

					# Удаляем сам вопрос
					$pdo->prepare("DELETE FROM `tbl_questions` WHERE `id` = '".$question['id']."'")->execute();
	
				}

				# удаляем эту группу вопросов
				$pdo->prepare("DELETE FROM `tbl_group_questions` WHERE `quiz_id` = '".$quiz['quiz_id']."'")->execute();

		}

		# удаляем комменты к опросу
		$pdo->prepare("DELETE FROM `tbl_quiz_comments` WHERE `quiz_id` = '".$quiz['quiz_id']."'")->execute();

		# удаляем этот опрос
		$pdo->prepare("DELETE FROM `tbl_quiz` WHERE `quiz_id` = '".$quiz['quiz_id']."'")->execute();
	}



	

}

?>