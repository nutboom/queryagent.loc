<?php
require(dirname(__FILE__).'/mailer.php');
$mailer_form = "admin@queryagent.ru";

$config		=	require(dirname(__FILE__).'/protected/config/local/main.php');
$pdo		=	new PDO($config['db']['connectionString'], $config['db']['username'], $config['db']['password']);
$pdo->exec("set names utf8");

# за неделю
$sql = $pdo->query("SELECT `tbl_licenses`.`id` as `id`, `tbl_users`.`email` as `email` FROM `tbl_licenses`, `tbl_users` WHERE DATEDIFF(`tbl_licenses`.`date_expirate`, NOW()) = 7 AND `tbl_licenses`.`active` = 'yes' AND `tbl_users`.`id`= `tbl_licenses`.`user`");
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $mailer = new Mailer();

    $mailer->from	=	$mailer_form;
	$mailer->to		=	$row['email'];
	$mailer->subject=	"До конца вашей лицензии осталось 7 дней";

    $mailer->html = "До конца вашей лицензии осталось 7 дней";

	$mailer->send();
}

# за 3 дня
$sql = $pdo->query("SELECT `tbl_licenses`.`id` as `id`, `tbl_users`.`email` as `email` FROM `tbl_licenses`, `tbl_users` WHERE DATEDIFF(`tbl_licenses`.`date_expirate`, NOW()) = 3 AND `tbl_licenses`.`active` = 'yes' AND `tbl_users`.`id`= `tbl_licenses`.`user`");
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $mailer = new Mailer();

    $mailer->from	=	$mailer_form;
	$mailer->to		=	$row['email'];
	$mailer->subject=	"До конца вашей лицензии осталось 3 дня";

    $mailer->html = "До конца вашей лицензии осталось 3 дня";

	$mailer->send();
}

# блокировка
$sql = $pdo->query("SELECT `tbl_licenses`.`id` as `id`, `tbl_users`.`email` as `email` FROM `tbl_licenses`, `tbl_users` WHERE DATEDIFF(`tbl_licenses`.`date_expirate`, NOW()) <= 0 AND `tbl_licenses`.`active` = 'yes' AND `tbl_users`.`id`= `tbl_licenses`.`user`");
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
	# блокируем лицензию
	$update = $pdo->prepare("
		UPDATE `tbl_licenses` SET
			`active`	=	'no'
		WHERE
			`id` = :id
	");
	$update->bindValue(":id", $row['id']);
	$update->execute();

    $mailer = new Mailer();

    $mailer->from	=	$mailer_form;
	$mailer->to		=	$row['email'];
	$mailer->subject=	"Ваша лицензия заблокирована";

    $mailer->html = "Ваша лицензия заблокирована";

	$mailer->send();
}
?>


